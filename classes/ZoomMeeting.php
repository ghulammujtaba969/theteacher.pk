<?php
require_once 'config/database.php';

class ZoomMeeting
{
    private $conn;
    private $table_name = "zoom_meetings";
    public $last_error = null;

    // Basic properties
    public $id;
    public $meeting_title;
    public $meeting_description;
    public $class_id;
    public $subject_id;
    public $syllabus_id;
    public $lecture_id;
    public $zoom_meeting_id;
    public $meeting_url;
    public $passcode;
    public $scheduled_date;
    public $duration_minutes;
    public $host_email;
    public $max_participants;
    public $created_by;
    public $status;

    // Security & Access Control
    public $waiting_room;
    public $join_before_host;
    public $approval_type; // 0 = automatic, 1 = manual, 2 = no registration

    // Audio/Video Settings
    public $mute_upon_entry;
    public $host_video;
    public $participant_video;
    public $audio_type; // both, voip, telephony

    // Participant Controls
    public $allow_multiple_devices;
    public $screen_sharing; // all, host
    public $enable_chat;
    public $enable_private_chat;
    public $enable_raise_hand;
    public $enable_reactions;
    public $enable_breakout_rooms;

    // Recording
    public $auto_recording; // none, local, cloud

    // Recurring Meeting Properties
    public $is_recurring;
    public $recurrence_type; // daily, weekly, monthly
    public $recurrence_interval; // repeat every X days/weeks/months
    public $recurrence_days; // days of week for weekly (e.g., "1,2,3,4,5" for weekdays)
    public $recurrence_end_date; // when recurrence ends
    public $recurrence_end_times; // number of occurrences
    public $parent_meeting_id; // for linking recurring instances

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        // Check if required Zoom credentials are configured
        if (empty(ZOOM_ACCOUNT_ID) || empty(ZOOM_CLIENT_ID) || empty(ZOOM_CLIENT_SECRET)) {
            $this->last_error = 'Zoom API credentials are not configured. Please check your .env file.';
            error_log('Zoom API Error: Missing credentials');
            // Save to database without Zoom integration
            return $this->saveToDatabase();
        }
        
        // Try to create meeting in Zoom
        require_once 'classes/ZoomAPI.php';
        
        try {
            $zoomAPI = new ZoomAPI();
            
            $meetingData = [
                'topic' => $this->meeting_title,
                'start_time' => $this->scheduled_date,
                'duration' => $this->duration_minutes,
                'password' => $this->passcode,
                'agenda' => $this->meeting_description,
                'settings' => $this->buildZoomSettings(),
                'type' => $this->is_recurring ? 8 : 2, // 8 = recurring, 2 = scheduled
            ];
            
            // Add recurrence settings if this is a recurring meeting
            if ($this->is_recurring) {
                $recurrence = $this->buildRecurrenceSettings();
                if ($recurrence) {
                    $meetingData['recurrence'] = $recurrence;
                }
            }
            
            error_log('Attempting to create Zoom meeting: ' . $this->meeting_title);
            
            $zoomResponse = $zoomAPI->createMeeting($meetingData);
            
            if ($zoomResponse && isset($zoomResponse['id'])) {
                // Update object with Zoom response data
                $this->zoom_meeting_id = $zoomResponse['id'];
                $this->meeting_url = $zoomResponse['join_url'];
                $this->passcode = $zoomResponse['password'] ?? $this->passcode;
                $this->host_email = $zoomResponse['host_email'] ?? $this->host_email;
                
                error_log('Zoom meeting created successfully: ID ' . $this->zoom_meeting_id);
                
                // Save to database
                return $this->saveToDatabase();
            } else {
                throw new Exception('Invalid response from Zoom API');
            }
        } catch (Exception $e) {
            $error_message = $e->getMessage();
            $this->last_error = 'Failed to create meeting in Zoom: ' . $error_message;
            error_log('Zoom Meeting Creation Error: ' . $error_message);
            
            // Don't save to database if Zoom creation fails
            return false;
        }
    }
    
    /**
     * Build Zoom settings array from object properties
     */
    private function buildZoomSettings()
    {
        $settings = [
            'host_video' => (bool)($this->host_video ?? true),
            'participant_video' => (bool)($this->participant_video ?? false),
            'join_before_host' => (bool)($this->join_before_host ?? false),
            'mute_upon_entry' => (bool)($this->mute_upon_entry ?? true),
            'waiting_room' => (bool)($this->waiting_room ?? true),
            'audio' => $this->audio_type ?? 'both',
            'auto_recording' => $this->auto_recording ?? 'none',
            'approval_type' => (int)($this->approval_type ?? 2),
            'meeting_authentication' => false,
            'enforce_login' => false,
        ];

        // Participant interaction controls
        if (isset($this->allow_multiple_devices)) {
            $settings['allow_multiple_devices'] = (bool)$this->allow_multiple_devices;
        }

        // Screen sharing permissions
        if (isset($this->screen_sharing)) {
            $settings['who_can_share_screen'] = $this->screen_sharing;
            $settings['who_can_share_screen_when_someone_is_sharing'] = $this->screen_sharing === 'host' ? 'host' : 'all';
        }

        return $settings;
    }
    
    /**
     * Build recurrence settings for Zoom API
     */
    private function buildRecurrenceSettings()
    {
        if (!$this->is_recurring) {
            return null;
        }

        $recurrence = [
            'type' => 1, // Default to daily
        ];

        // Map our recurrence type to Zoom's type
        switch ($this->recurrence_type) {
            case 'daily':
                $recurrence['type'] = 1;
                $recurrence['repeat_interval'] = (int)($this->recurrence_interval ?? 1);
                break;
            case 'weekly':
                $recurrence['type'] = 2;
                $recurrence['repeat_interval'] = (int)($this->recurrence_interval ?? 1);
                if (!empty($this->recurrence_days)) {
                    $recurrence['weekly_days'] = $this->recurrence_days;
                }
                break;
            case 'monthly':
                $recurrence['type'] = 3;
                $recurrence['repeat_interval'] = (int)($this->recurrence_interval ?? 1);
                break;
        }

        // End date or end times
        if (!empty($this->recurrence_end_times)) {
            $recurrence['end_times'] = (int)$this->recurrence_end_times;
        } elseif (!empty($this->recurrence_end_date)) {
            $recurrence['end_date_time'] = date('Y-m-d\TH:i:s\Z', strtotime($this->recurrence_end_date));
        }

        return $recurrence;
    }
    
    /**
     * Save meeting to database
     */
    private function saveToDatabase()
    {
        $query = "INSERT INTO " . $this->table_name . "
                  SET meeting_title=:meeting_title, meeting_description=:meeting_description,
                      class_id=:class_id, subject_id=:subject_id, syllabus_id=:syllabus_id,
                      lecture_id=:lecture_id, zoom_meeting_id=:zoom_meeting_id,
                      meeting_url=:meeting_url, passcode=:passcode,
                      scheduled_date=:scheduled_date, duration_minutes=:duration_minutes,
                      host_email=:host_email, max_participants=:max_participants,
                      created_by=:created_by, status=:status,
                      is_recurring=:is_recurring, recurrence_type=:recurrence_type,
                      recurrence_interval=:recurrence_interval, recurrence_days=:recurrence_days,
                      recurrence_end_date=:recurrence_end_date, recurrence_end_times=:recurrence_end_times,
                      parent_meeting_id=:parent_meeting_id,
                      waiting_room=:waiting_room, join_before_host=:join_before_host,
                      mute_upon_entry=:mute_upon_entry, host_video=:host_video,
                      participant_video=:participant_video, auto_recording=:auto_recording,
                      approval_type=:approval_type, audio_type=:audio_type,
                      allow_multiple_devices=:allow_multiple_devices, screen_sharing=:screen_sharing,
                      enable_chat=:enable_chat, enable_private_chat=:enable_private_chat,
                      enable_raise_hand=:enable_raise_hand, enable_reactions=:enable_reactions,
                      enable_breakout_rooms=:enable_breakout_rooms";

        $stmt = $this->conn->prepare($query);

        // Bind basic parameters
        $stmt->bindParam(':meeting_title', $this->meeting_title);
        $stmt->bindParam(':meeting_description', $this->meeting_description);
        $stmt->bindParam(':class_id', $this->class_id);
        $stmt->bindParam(':subject_id', $this->subject_id);
        $stmt->bindParam(':syllabus_id', $this->syllabus_id);
        $stmt->bindParam(':lecture_id', $this->lecture_id);
        $stmt->bindParam(':zoom_meeting_id', $this->zoom_meeting_id);
        $stmt->bindParam(':meeting_url', $this->meeting_url);
        $stmt->bindParam(':passcode', $this->passcode);
        $stmt->bindParam(':scheduled_date', $this->scheduled_date);
        $stmt->bindParam(':duration_minutes', $this->duration_minutes);
        $stmt->bindParam(':host_email', $this->host_email);
        $stmt->bindParam(':max_participants', $this->max_participants);
        $stmt->bindParam(':created_by', $this->created_by);
        $this->status = 'scheduled';
        $stmt->bindParam(':status', $this->status);

        // Bind recurring meeting parameters
        $is_recurring = (int)($this->is_recurring ?? 0);
        $stmt->bindParam(':is_recurring', $is_recurring);
        $stmt->bindParam(':recurrence_type', $this->recurrence_type);
        $recurrence_interval = (int)($this->recurrence_interval ?? 1);
        $stmt->bindParam(':recurrence_interval', $recurrence_interval);
        $stmt->bindParam(':recurrence_days', $this->recurrence_days);
        $stmt->bindParam(':recurrence_end_date', $this->recurrence_end_date);
        $recurrence_end_times = $this->recurrence_end_times ? (int)$this->recurrence_end_times : null;
        $stmt->bindParam(':recurrence_end_times', $recurrence_end_times);
        $parent_meeting_id = $this->parent_meeting_id ? (int)$this->parent_meeting_id : null;
        $stmt->bindParam(':parent_meeting_id', $parent_meeting_id);

        // Bind security & access parameters
        $waiting_room = (int)($this->waiting_room ?? 1);
        $join_before_host = (int)($this->join_before_host ?? 0);
        $approval_type = (int)($this->approval_type ?? 2);
        $stmt->bindParam(':waiting_room', $waiting_room);
        $stmt->bindParam(':join_before_host', $join_before_host);
        $stmt->bindParam(':approval_type', $approval_type);

        // Bind audio/video parameters
        $mute_upon_entry = (int)($this->mute_upon_entry ?? 1);
        $host_video = (int)($this->host_video ?? 1);
        $participant_video = (int)($this->participant_video ?? 0);
        $audio_type = $this->audio_type ?? 'both';
        $stmt->bindParam(':mute_upon_entry', $mute_upon_entry);
        $stmt->bindParam(':host_video', $host_video);
        $stmt->bindParam(':participant_video', $participant_video);
        $stmt->bindParam(':audio_type', $audio_type);

        // Bind recording parameters
        $auto_recording = $this->auto_recording ?? 'none';
        $stmt->bindParam(':auto_recording', $auto_recording);

        // Bind participant control parameters
        $allow_multiple_devices = (int)($this->allow_multiple_devices ?? 0);
        $screen_sharing = $this->screen_sharing ?? 'all';
        $enable_chat = (int)($this->enable_chat ?? 1);
        $enable_private_chat = (int)($this->enable_private_chat ?? 1);
        $enable_raise_hand = (int)($this->enable_raise_hand ?? 1);
        $enable_reactions = (int)($this->enable_reactions ?? 1);
        $enable_breakout_rooms = (int)($this->enable_breakout_rooms ?? 0);
        
        $stmt->bindParam(':allow_multiple_devices', $allow_multiple_devices);
        $stmt->bindParam(':screen_sharing', $screen_sharing);
        $stmt->bindParam(':enable_chat', $enable_chat);
        $stmt->bindParam(':enable_private_chat', $enable_private_chat);
        $stmt->bindParam(':enable_raise_hand', $enable_raise_hand);
        $stmt->bindParam(':enable_reactions', $enable_reactions);
        $stmt->bindParam(':enable_breakout_rooms', $enable_breakout_rooms);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        $this->last_error = 'Failed to save meeting to database';
        return false;
    }

    public function read($accessible_class_ids = [], $user_role = '')
    {
        $query = "SELECT zm.*, c.class_name, s.subject_name, sy.syllabus_title, l.lecture_title,
                     u.full_name as creator_name
              FROM " . $this->table_name . " zm
              LEFT JOIN classes c ON zm.class_id = c.id
              LEFT JOIN subjects s ON zm.subject_id = s.id
              LEFT JOIN syllabi sy ON zm.syllabus_id = sy.id
              LEFT JOIN lectures l ON zm.lecture_id = l.id
              LEFT JOIN users u ON zm.created_by = u.id
              WHERE zm.status != 'cancelled'";

        // Only filter by class access if user is not super admin
        if ($user_role !== 'super_admin' && !empty($accessible_class_ids)) {
            $placeholders = implode(',', array_fill(0, count($accessible_class_ids), '?'));
            $query .= " AND (zm.class_id IN (" . $placeholders . ") OR zm.class_id IS NULL)";
        }

        $query .= " ORDER BY zm.scheduled_date ASC";

        $stmt = $this->conn->prepare($query);
        if ($user_role !== 'super_admin' && !empty($accessible_class_ids)) {
            $stmt->execute($accessible_class_ids);
        } else {
            $stmt->execute();
        }

        return $stmt;
    }

    public function readOne()
    {
        $query = "SELECT zm.*, c.class_name, s.subject_name, sy.syllabus_title, l.lecture_title,
                         u.full_name as creator_name
                  FROM " . $this->table_name . " zm
                  LEFT JOIN classes c ON zm.class_id = c.id
                  LEFT JOIN subjects s ON zm.subject_id = s.id
                  LEFT JOIN syllabi sy ON zm.syllabus_id = sy.id
                  LEFT JOIN lectures l ON zm.lecture_id = l.id
                  LEFT JOIN users u ON zm.created_by = u.id
                  WHERE zm.id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->id]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            
            // Basic properties
            $this->meeting_title = $row['meeting_title'];
            $this->meeting_description = $row['meeting_description'];
            $this->class_id = $row['class_id'];
            $this->subject_id = $row['subject_id'];
            $this->syllabus_id = $row['syllabus_id'];
            $this->lecture_id = $row['lecture_id'];
            $this->zoom_meeting_id = $row['zoom_meeting_id'];
            $this->meeting_url = $row['meeting_url'];
            $this->passcode = $row['passcode'];
            $this->scheduled_date = $row['scheduled_date'];
            $this->duration_minutes = $row['duration_minutes'];
            $this->host_email = $row['host_email'];
            $this->max_participants = $row['max_participants'];
            $this->created_by = $row['created_by'];
            $this->status = $row['status'];

            // Security & Access
            $this->waiting_room = $row['waiting_room'] ?? 1;
            $this->join_before_host = $row['join_before_host'] ?? 0;
            $this->approval_type = $row['approval_type'] ?? 2;

            // Audio/Video
            $this->mute_upon_entry = $row['mute_upon_entry'] ?? 1;
            $this->host_video = $row['host_video'] ?? 1;
            $this->participant_video = $row['participant_video'] ?? 0;
            $this->audio_type = $row['audio_type'] ?? 'both';

            // Recording
            $this->auto_recording = $row['auto_recording'] ?? 'none';

            // Recurring Meeting Settings
            $this->is_recurring = $row['is_recurring'] ?? 0;
            $this->recurrence_type = $row['recurrence_type'] ?? null;
            $this->recurrence_interval = $row['recurrence_interval'] ?? 1;
            $this->recurrence_days = $row['recurrence_days'] ?? null;
            $this->recurrence_end_date = $row['recurrence_end_date'] ?? null;
            $this->recurrence_end_times = $row['recurrence_end_times'] ?? null;
            $this->parent_meeting_id = $row['parent_meeting_id'] ?? null;

            // Participant Controls
            $this->allow_multiple_devices = $row['allow_multiple_devices'] ?? 0;
            $this->screen_sharing = $row['screen_sharing'] ?? 'all';
            $this->enable_chat = $row['enable_chat'] ?? 1;
            $this->enable_private_chat = $row['enable_private_chat'] ?? 1;
            $this->enable_raise_hand = $row['enable_raise_hand'] ?? 1;
            $this->enable_reactions = $row['enable_reactions'] ?? 1;
            $this->enable_breakout_rooms = $row['enable_breakout_rooms'] ?? 0;

            return true;
        }
        return false;
    }

    public function update()
    {
        // First update meeting in Zoom if we have a zoom_meeting_id
        if (!empty($this->zoom_meeting_id)) {
            require_once 'classes/ZoomAPI.php';
            
            try {
                $zoomAPI = new ZoomAPI();
                
                $meetingData = [
                    'topic' => $this->meeting_title,
                    'start_time' => $this->scheduled_date,
                    'duration' => $this->duration_minutes,
                    'password' => $this->passcode,
                    'agenda' => $this->meeting_description,
                    'settings' => $this->buildZoomSettings(),
                    'type' => $this->is_recurring ? 8 : 2, // 8 = recurring, 2 = scheduled
                ];
                
                // Add recurrence settings if this is a recurring meeting
                if ($this->is_recurring) {
                    $recurrence = $this->buildRecurrenceSettings();
                    if ($recurrence) {
                        $meetingData['recurrence'] = $recurrence;
                    }
                }
                
                $zoomResponse = $zoomAPI->updateMeeting($this->zoom_meeting_id, $meetingData);
                
                error_log('Zoom meeting updated successfully: ' . $this->zoom_meeting_id);
            } catch (Exception $e) {
                $this->last_error = 'Failed to update meeting in Zoom: ' . $e->getMessage();
                error_log('Zoom Meeting Update Error: ' . $e->getMessage());
                // Continue with database update even if Zoom API fails
            }
        }
        
        // Update in database
        $query = "UPDATE " . $this->table_name . "
                  SET meeting_title=:meeting_title, meeting_description=:meeting_description,
                      class_id=:class_id, subject_id=:subject_id, syllabus_id=:syllabus_id,
                      lecture_id=:lecture_id, zoom_meeting_id=:zoom_meeting_id,
                      meeting_url=:meeting_url, passcode=:passcode,
                      scheduled_date=:scheduled_date, duration_minutes=:duration_minutes,
                      host_email=:host_email, max_participants=:max_participants,
                      is_recurring=:is_recurring, recurrence_type=:recurrence_type,
                      recurrence_interval=:recurrence_interval, recurrence_days=:recurrence_days,
                      recurrence_end_date=:recurrence_end_date, recurrence_end_times=:recurrence_end_times,
                      waiting_room=:waiting_room, join_before_host=:join_before_host,
                      mute_upon_entry=:mute_upon_entry, host_video=:host_video,
                      participant_video=:participant_video, auto_recording=:auto_recording,
                      approval_type=:approval_type, audio_type=:audio_type,
                      allow_multiple_devices=:allow_multiple_devices, screen_sharing=:screen_sharing,
                      enable_chat=:enable_chat, enable_private_chat=:enable_private_chat,
                      enable_raise_hand=:enable_raise_hand, enable_reactions=:enable_reactions,
                      enable_breakout_rooms=:enable_breakout_rooms
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Bind all parameters
        $stmt->bindParam(':meeting_title', $this->meeting_title);
        $stmt->bindParam(':meeting_description', $this->meeting_description);
        $stmt->bindParam(':class_id', $this->class_id);
        $stmt->bindParam(':subject_id', $this->subject_id);
        $stmt->bindParam(':syllabus_id', $this->syllabus_id);
        $stmt->bindParam(':lecture_id', $this->lecture_id);
        $stmt->bindParam(':zoom_meeting_id', $this->zoom_meeting_id);
        $stmt->bindParam(':meeting_url', $this->meeting_url);
        $stmt->bindParam(':passcode', $this->passcode);
        $stmt->bindParam(':scheduled_date', $this->scheduled_date);
        $stmt->bindParam(':duration_minutes', $this->duration_minutes);
        $stmt->bindParam(':host_email', $this->host_email);
        $stmt->bindParam(':max_participants', $this->max_participants);
        
        // Recurring Meeting Parameters
        $is_recurring = (int)($this->is_recurring ?? 0);
        $stmt->bindParam(':is_recurring', $is_recurring);
        $stmt->bindParam(':recurrence_type', $this->recurrence_type);
        $recurrence_interval = (int)($this->recurrence_interval ?? 1);
        $stmt->bindParam(':recurrence_interval', $recurrence_interval);
        $stmt->bindParam(':recurrence_days', $this->recurrence_days);
        $stmt->bindParam(':recurrence_end_date', $this->recurrence_end_date);
        $recurrence_end_times = $this->recurrence_end_times ? (int)$this->recurrence_end_times : null;
        $stmt->bindParam(':recurrence_end_times', $recurrence_end_times);
        
        // Security & Access
        $stmt->bindParam(':waiting_room', $this->waiting_room);
        $stmt->bindParam(':join_before_host', $this->join_before_host);
        $stmt->bindParam(':approval_type', $this->approval_type);
        
        // Audio/Video
        $stmt->bindParam(':mute_upon_entry', $this->mute_upon_entry);
        $stmt->bindParam(':host_video', $this->host_video);
        $stmt->bindParam(':participant_video', $this->participant_video);
        $stmt->bindParam(':audio_type', $this->audio_type);
        
        // Recording
        $stmt->bindParam(':auto_recording', $this->auto_recording);
        
        // Participant Controls
        $stmt->bindParam(':allow_multiple_devices', $this->allow_multiple_devices);
        $stmt->bindParam(':screen_sharing', $this->screen_sharing);
        $stmt->bindParam(':enable_chat', $this->enable_chat);
        $stmt->bindParam(':enable_private_chat', $this->enable_private_chat);
        $stmt->bindParam(':enable_raise_hand', $this->enable_raise_hand);
        $stmt->bindParam(':enable_reactions', $this->enable_reactions);
        $stmt->bindParam(':enable_breakout_rooms', $this->enable_breakout_rooms);
        
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete()
    {
        // First delete meeting from Zoom if we have a zoom_meeting_id
        if (!empty($this->zoom_meeting_id)) {
            require_once 'classes/ZoomAPI.php';
            
            try {
                $zoomAPI = new ZoomAPI();
                $zoomResponse = $zoomAPI->deleteMeeting($this->zoom_meeting_id);
                
                error_log('Zoom meeting deleted successfully: ' . $this->zoom_meeting_id);
            } catch (Exception $e) {
                $this->last_error = 'Failed to delete meeting in Zoom: ' . $e->getMessage();
                error_log('Zoom Meeting Delete Error: ' . $e->getMessage());
                // Continue with database update even if Zoom API fails
            }
        }
        
        // Update status in database (soft delete)
        $query = "UPDATE " . $this->table_name . " SET status = 'cancelled' WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->id]);
    }
    
    /**
     * Get the last error message
     */
    public function getLastError()
    {
        return $this->last_error;
    }

    public function getCount()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE status != 'cancelled'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }

    public function getUpcomingCount()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                  WHERE status = 'scheduled' AND scheduled_date > NOW()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }
}
?>