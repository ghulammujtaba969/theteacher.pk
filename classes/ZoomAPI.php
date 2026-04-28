<?php
class ZoomAPI {
    private $access_token;
    private $base_url;
    private $last_error;
    
    public function __construct() {
        $this->base_url = ZOOM_API_BASE_URL;
        $this->last_error = null;
        
        try {
            $this->access_token = $this->getAccessToken();
        } catch (Exception $e) {
            $this->last_error = $e->getMessage();
            throw $e;
        }
    }
    
    /**
     * Get OAuth access token using Server-to-Server OAuth
     */
    private function getAccessToken() {
        $url = 'https://zoom.us/oauth/token';
        
        $data = [
            'grant_type' => 'account_credentials',
            'account_id' => ZOOM_ACCOUNT_ID
        ];
        
        $headers = [
            'Authorization: Basic ' . base64_encode(ZOOM_CLIENT_ID . ':' . ZOOM_CLIENT_SECRET),
            'Content-Type: application/x-www-form-urlencoded'
        ];
        
        $response = $this->makeRequest($url, 'POST', $data, $headers);
        
        if ($response === false) {
            throw new Exception('Failed to connect to Zoom API. Check your internet connection and API credentials.');
        }
        
        if (!isset($response['access_token'])) {
            $error_msg = isset($response['message']) ? $response['message'] : 'Unknown error';
            $error_code = isset($response['code']) ? $response['code'] : 'N/A';
            throw new Exception("Failed to get Zoom access token. Error: $error_msg (Code: $error_code)");
        }
        
        return $response['access_token'];
    }
    
    /**
     * Create a new Zoom meeting with comprehensive settings
     */
    public function createMeeting($meetingData) {
        $url = $this->base_url . '/users/me/meetings';
        
        // Format the start time properly
        $start_time = $this->formatDateTime($meetingData['start_time']);
        
        // Build comprehensive meeting settings
        $settings = isset($meetingData['settings']) ? $meetingData['settings'] : [];
        
        // Default settings if not provided
        $defaultSettings = [
            'host_video' => true,
            'participant_video' => false,
            'join_before_host' => false,
            'mute_upon_entry' => true,
            'waiting_room' => true,
            'audio' => 'both',
            'auto_recording' => 'none',
            'approval_type' => 2,
            'meeting_authentication' => false,
            'enforce_login' => false,
        ];
        
        // Merge with provided settings
        $finalSettings = array_merge($defaultSettings, $settings);
        
        // Determine meeting type
        $meetingType = isset($meetingData['type']) ? $meetingData['type'] : 2;
        
        $data = [
            'topic' => $meetingData['topic'],
            'type' => $meetingType, // 2 = scheduled, 8 = recurring with fixed time
            'start_time' => $start_time,
            'duration' => (int)$meetingData['duration'],
            'timezone' => 'UTC',
            'settings' => $finalSettings
        ];
        
        // Add recurrence settings if this is a recurring meeting
        if ($meetingType == 8 && isset($meetingData['recurrence'])) {
            $data['recurrence'] = $meetingData['recurrence'];
        }
        
        if (!empty($meetingData['password'])) {
            $data['password'] = $meetingData['password'];
        }
        
        if (!empty($meetingData['agenda'])) {
            $data['agenda'] = $meetingData['agenda'];
        }
        
        $headers = [
            'Authorization: Bearer ' . $this->access_token,
            'Content-Type: application/json'
        ];
        
        $response = $this->makeRequest($url, 'POST', $data, $headers);
        
        if ($response === false) {
            throw new Exception('Failed to create Zoom meeting. API request failed.');
        }
        
        if (isset($response['code']) && $response['code'] !== 201) {
            $error_msg = isset($response['message']) ? $response['message'] : 'Unknown error';
            throw new Exception("Zoom API Error: $error_msg");
        }
        
        if (!isset($response['id']) || !isset($response['join_url'])) {
            throw new Exception('Invalid response from Zoom API. Meeting may not have been created.');
        }
        
        return $response;
    }
    
    /**
     * Update an existing Zoom meeting with comprehensive settings
     */
    public function updateMeeting($meetingId, $meetingData) {
        $url = $this->base_url . '/meetings/' . $meetingId;
        
        $start_time = $this->formatDateTime($meetingData['start_time']);
        
        // Build comprehensive meeting settings
        $settings = isset($meetingData['settings']) ? $meetingData['settings'] : [];
        
        // Default settings if not provided
        $defaultSettings = [
            'host_video' => true,
            'participant_video' => false,
            'join_before_host' => false,
            'mute_upon_entry' => true,
            'waiting_room' => true,
            'audio' => 'both',
            'auto_recording' => 'none',
            'approval_type' => 2,
        ];
        
        // Merge with provided settings
        $finalSettings = array_merge($defaultSettings, $settings);
        
        // Determine meeting type
        $meetingType = isset($meetingData['type']) ? $meetingData['type'] : 2;
        
        $data = [
            'topic' => $meetingData['topic'],
            'type' => $meetingType,
            'start_time' => $start_time,
            'duration' => (int)$meetingData['duration'],
            'timezone' => 'UTC',
            'settings' => $finalSettings
        ];
        
        // Add recurrence settings if this is a recurring meeting
        if ($meetingType == 8 && isset($meetingData['recurrence'])) {
            $data['recurrence'] = $meetingData['recurrence'];
        }
        
        if (!empty($meetingData['password'])) {
            $data['password'] = $meetingData['password'];
        }
        
        if (!empty($meetingData['agenda'])) {
            $data['agenda'] = $meetingData['agenda'];
        }
        
        $headers = [
            'Authorization: Bearer ' . $this->access_token,
            'Content-Type: application/json'
        ];
        
        $response = $this->makeRequest($url, 'PATCH', $data, $headers);
        
        if ($response === false) {
            throw new Exception('Failed to update Zoom meeting. API request failed.');
        }
        
        return $response;
    }
    
    /**
     * Update meeting settings specifically
     */
    public function updateMeetingSettings($meetingId, $settings) {
        $url = $this->base_url . '/meetings/' . $meetingId;
        
        $data = [
            'settings' => $settings
        ];
        
        $headers = [
            'Authorization: Bearer ' . $this->access_token,
            'Content-Type: application/json'
        ];
        
        $response = $this->makeRequest($url, 'PATCH', $data, $headers);
        
        if ($response === false) {
            throw new Exception('Failed to update meeting settings. API request failed.');
        }
        
        return $response;
    }
    
    /**
     * Get waiting room participants
     */
    public function getWaitingRoomParticipants($meetingId) {
        $url = $this->base_url . '/meetings/' . $meetingId . '/waiting_room_participants';
        
        $headers = [
            'Authorization: Bearer ' . $this->access_token
        ];
        
        return $this->makeRequest($url, 'GET', null, $headers);
    }
    
    /**
     * Admit participant from waiting room
     */
    public function admitWaitingRoomParticipant($meetingId, $participantId) {
        $url = $this->base_url . '/meetings/' . $meetingId . '/waiting_room_participants/' . $participantId;
        
        $data = [
            'action' => 'admit'
        ];
        
        $headers = [
            'Authorization: Bearer ' . $this->access_token,
            'Content-Type: application/json'
        ];
        
        return $this->makeRequest($url, 'PUT', $data, $headers);
    }
    
    /**
     * Get meeting participants
     */
    public function getParticipants($meetingId) {
        $url = $this->base_url . '/metrics/meetings/' . $meetingId . '/participants';
        
        $headers = [
            'Authorization: Bearer ' . $this->access_token
        ];
        
        return $this->makeRequest($url, 'GET', null, $headers);
    }
    
    /**
     * Delete a Zoom meeting
     */
    public function deleteMeeting($meetingId) {
        $url = $this->base_url . '/meetings/' . $meetingId;
        
        $headers = [
            'Authorization: Bearer ' . $this->access_token
        ];
        
        $response = $this->makeRequest($url, 'DELETE', null, $headers);
        
        if ($response === false) {
            throw new Exception('Failed to delete Zoom meeting. API request failed.');
        }
        
        return $response;
    }
    
    /**
     * Get meeting details
     */
    public function getMeeting($meetingId) {
        $url = $this->base_url . '/meetings/' . $meetingId;
        
        $headers = [
            'Authorization: Bearer ' . $this->access_token
        ];
        
        return $this->makeRequest($url, 'GET', null, $headers);
    }
    
    /**
     * List user's meetings
     */
    public function listMeetings($userId = 'me', $type = 'scheduled') {
        $url = $this->base_url . '/users/' . $userId . '/meetings?type=' . $type;
        
        $headers = [
            'Authorization: Bearer ' . $this->access_token
        ];
        
        return $this->makeRequest($url, 'GET', null, $headers);
    }
    
    /**
     * Get meeting recording
     */
    public function getMeetingRecordings($meetingId) {
        $url = $this->base_url . '/meetings/' . $meetingId . '/recordings';
        
        $headers = [
            'Authorization: Bearer ' . $this->access_token
        ];
        
        return $this->makeRequest($url, 'GET', null, $headers);
    }
    
    /**
     * Update participant permissions during meeting
     */
    public function updateParticipantPermissions($meetingId, $participantId, $permissions) {
        $url = $this->base_url . '/live_meetings/' . $meetingId . '/events';
        
        $data = [
            'method' => 'participant.update',
            'params' => [
                'participant' => [
                    'id' => $participantId,
                ] + $permissions
            ]
        ];
        
        $headers = [
            'Authorization: Bearer ' . $this->access_token,
            'Content-Type: application/json'
        ];
        
        return $this->makeRequest($url, 'PATCH', $data, $headers);
    }
    
    /**
     * Format datetime for Zoom API
     * Zoom expects: YYYY-MM-DDTHH:MM:SSZ for UTC
     */
    private function formatDateTime($datetime) {
        try {
            $dt = new DateTime($datetime);
            // Ensure we're using UTC
            $dt->setTimezone(new DateTimeZone('UTC'));
            return $dt->format('Y-m-d\TH:i:s\Z');
        } catch (Exception $e) {
            throw new Exception("Invalid datetime format: $datetime");
        }
    }
    
    /**
     * Make HTTP request with better error handling
     */
    private function makeRequest($url, $method = 'GET', $data = null, $headers = []) {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        
        if ($data && ($method === 'POST' || $method === 'PATCH' || $method === 'PUT')) {
            if (in_array('Content-Type: application/json', $headers)) {
                $json_data = json_encode($data);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
                
                // Debug: Log the request
                error_log("Zoom API Request to $url: " . $json_data);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            }
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        // Debug: Log the response
        error_log("Zoom API Response (HTTP $httpCode): " . $response);
        
        if ($error) {
            error_log('Zoom API cURL Error: ' . $error);
            $this->last_error = "Connection error: $error";
            return false;
        }
        
        if ($httpCode >= 400) {
            error_log("Zoom API HTTP Error: $httpCode - Response: $response");
            $decoded = json_decode($response, true);
            
            if ($decoded) {
                $this->last_error = isset($decoded['message']) ? $decoded['message'] : "HTTP Error $httpCode";
                return $decoded; // Return the error response for detailed handling
            }
            
            $this->last_error = "HTTP Error $httpCode";
            return false;
        }
        
        // For DELETE requests, empty response is OK
        if ($method === 'DELETE' && $httpCode === 204) {
            return ['success' => true];
        }
        
        $decoded = json_decode($response, true);
        
        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            error_log('Zoom API JSON decode error: ' . json_last_error_msg());
            $this->last_error = 'Invalid JSON response from Zoom API';
            return false;
        }
        
        return $decoded;
    }
    
    /**
     * Get the last error message
     */
    public function getLastError() {
        return $this->last_error;
    }
    
    /**
     * Test API connection
     */
    public function testConnection() {
        try {
            $url = $this->base_url . '/users/me';
            $headers = [
                'Authorization: Bearer ' . $this->access_token
            ];
            
            $response = $this->makeRequest($url, 'GET', null, $headers);
            
            if ($response === false) {
                return [
                    'success' => false,
                    'error' => $this->last_error
                ];
            }
            
            return [
                'success' => true,
                'user' => $response
            ];
        } catch (Exception $e) {
            error_log('Zoom API Test Failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
?>