<?php
require_once 'config/database.php';

class DashboardStats
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getStatsForUser($current_user)
    {
        $role_name = $current_user['role_name'];

        switch ($role_name) {
            case 'Super Admin':
                return $this->getSuperAdminStats($current_user);
            case 'Organization Admin':
                return $this->getOrganizationAdminStats($current_user);
            case 'School Admin':
                return $this->getSchoolAdminStats($current_user);
            case 'Teacher':
                return $this->getTeacherStats($current_user);
            case 'Solo Student':
                return $this->getSoloStudentStats($current_user);
            default:
                return $this->getDefaultStats();
        }
    }

    private function getSuperAdminStats($current_user)
    {
        $stats = [
            'organizations' => $this->getOrganizationsCount(),
            'schools' => $this->getSchoolsCount(),
            'users' => $this->getUsersCount(),
            'classes' => $this->getClassesCount(),
            'subjects' => $this->getSubjectsCount(),
            'syllabi' => $this->getSyllabiCount(),
            'lectures' => $this->getLecturesCount()
        ];

        $stats['widget_data'] = [
            [
                'title' => 'Organizations',
                'count' => $stats['organizations'],
                'icon' => 'ph-fill ph-buildings',
                'color' => '#3D7FF9',
                'chart_id' => 'organizations-chart',
                'image' => 'assets/images/dashboard/elearning.jpeg'
            ],
            [
                'title' => 'Schools',
                'count' => $stats['schools'],
                'icon' => 'ph-fill ph-graduation-cap',
                'color' => '#27CFA7',
                'chart_id' => 'schools-chart',
                'image' => 'assets/images/dashboard/Schools.jpeg'
            ],
            [
                'title' => 'Users',
                'count' => $stats['users'],
                'icon' => 'ph-fill ph-users-three',
                'color' => '#6142FF',
                'chart_id' => 'users-chart',
                'image' => 'assets/images/dashboard/students.jpeg'
            ],
            [
                'title' => 'Classes',
                'count' => $stats['classes'],
                'icon' => 'ph-fill ph-chalkboard-teacher',
                'color' => '#FA902F',
                'chart_id' => 'classes-chart',
                'image' => 'assets/images/dashboard/courses.jpeg'
            ]
        ];

        $stats['radial_chart_data'] = [
            'values' => [$stats['classes'], $stats['subjects'], $stats['lectures']],
            'labels' => ['Classes', 'Subjects', 'Lectures'],
            'colors' => ['#3D7FF9', '#27CFA7', '#020203'],
            'total' => $stats['classes'] + $stats['subjects'] + $stats['lectures']
        ];

        $stats['activity_stats'] = $this->getActivityStats($current_user);
        $stats['overview_bar_chart'] = $this->getOverviewBarChartData();

        return $stats;
    }

    private function getOrganizationAdminStats($current_user)
    {
        $org_id = $current_user['organization_id'];

        // Limit content stats to classes the org admin has access to
        require_once 'classes/User.php';
        $user = new User($this->db);
        $accessible_classes = $user->getAccessibleClasses($current_user);
        $accessible_class_ids = array_column($accessible_classes, 'id');

        $stats = [
            'schools' => $this->getSchoolsCountByOrganization($org_id),
            'users' => $this->getUsersCountByOrganization($org_id),
            'classes' => empty($accessible_class_ids) ? 0 : count($accessible_classes),
            'subjects' => empty($accessible_class_ids) ? 0 : $this->getSubjectsCountByClassIds($accessible_class_ids),
            'syllabi' => empty($accessible_class_ids) ? 0 : $this->getSyllabiCountByClassIds($accessible_class_ids),
            'lectures' => empty($accessible_class_ids) ? 0 : $this->getLecturesCountByClassIds($accessible_class_ids)
        ];

        $stats['widget_data'] = [
            [
                'title' => 'Schools',
                'count' => $stats['schools'],
                'icon' => 'ph-fill ph-graduation-cap',
                'color' => '#3D7FF9',
                'chart_id' => 'schools-chart',
                'image' => 'assets/images/dashboard/Schools.jpeg'
            ],
            [
                'title' => 'Users',
                'count' => $stats['users'],
                'icon' => 'ph-fill ph-users-three',
                'color' => '#27CFA7',
                'chart_id' => 'users-chart',
                'image' => 'assets/images/dashboard/students.jpeg'
            ],
            [
                'title' => 'Classes',
                'count' => $stats['classes'],
                'icon' => 'ph-fill ph-chalkboard-teacher',
                'color' => '#6142FF',
                'chart_id' => 'classes-chart',
                'image' => 'assets/images/dashboard/courses.jpeg'
            ],
            [
                'title' => 'Subjects',
                'count' => $stats['subjects'],
                'icon' => 'ph-fill ph-book-open',
                'color' => '#FA902F',
                'chart_id' => 'subjects-chart',
                'image' => 'assets/images/dashboard/elearning.jpeg'
            ]
        ];

        $stats['radial_chart_data'] = [
            'values' => [$stats['classes'], $stats['subjects'], $stats['lectures']],
            'labels' => ['Classes', 'Subjects', 'Lectures'],
            'colors' => ['#3D7FF9', '#27CFA7', '#020203'],
            'total' => $stats['classes'] + $stats['subjects'] + $stats['lectures']
        ];

        $stats['activity_stats'] = $this->getActivityStats($current_user);

        return $stats;
    }

    private function getSchoolAdminStats($current_user)
    {
        $school_id = $current_user['school_id'];

        require_once 'classes/User.php';
        $user = new User($this->db);
        $accessible_classes = $user->getAccessibleClasses($current_user);
        $accessible_class_ids = array_column($accessible_classes, 'id');

        $stats = [
            'users' => $this->getUsersCountBySchool($school_id),
            'classes' => empty($accessible_class_ids) ? 0 : count($accessible_classes),
            'subjects' => empty($accessible_class_ids) ? 0 : $this->getSubjectsCountByClassIds($accessible_class_ids),
            'syllabi' => empty($accessible_class_ids) ? 0 : $this->getSyllabiCountByClassIds($accessible_class_ids),
            'lectures' => empty($accessible_class_ids) ? 0 : $this->getLecturesCountByClassIds($accessible_class_ids)
        ];

        $stats['widget_data'] = [
            [
                'title' => 'Users',
                'count' => $stats['users'],
                'icon' => 'ph-fill ph-users-three',
                'color' => '#3D7FF9',
                'chart_id' => 'users-chart',
                'image' => 'assets/images/dashboard/students.jpeg'
            ],
            [
                'title' => 'Classes',
                'count' => $stats['classes'],
                'icon' => 'ph-fill ph-chalkboard-teacher',
                'color' => '#27CFA7',
                'chart_id' => 'classes-chart',
                'image' => 'assets/images/dashboard/courses.jpeg'
            ],
            [
                'title' => 'Subjects',
                'count' => $stats['subjects'],
                'icon' => 'ph-fill ph-book-open',
                'color' => '#6142FF',
                'chart_id' => 'subjects-chart',
                'image' => 'assets/images/dashboard/elearning.jpeg'
            ],
            [
                'title' => 'Lectures',
                'count' => $stats['lectures'],
                'icon' => 'ph-fill ph-play-circle',
                'color' => '#FA902F',
                'chart_id' => 'lectures-chart',
                'image' => 'assets/images/dashboard/elearning.jpeg'
            ]
        ];

        $stats['radial_chart_data'] = [
            'values' => [$stats['classes'], $stats['subjects'], $stats['lectures']],
            'labels' => ['Classes', 'Subjects', 'Lectures'],
            'colors' => ['#3D7FF9', '#27CFA7', '#020203'],
            'total' => $stats['classes'] + $stats['subjects'] + $stats['lectures']
        ];

        $stats['activity_stats'] = $this->getActivityStats($current_user);

        return $stats;
    }

    private function getTeacherStats($current_user)
    {
        // Teachers see only their accessible classes/content
        require_once 'classes/User.php';
        $user = new User($this->db);
        $accessible_classes = $user->getAccessibleClasses($current_user);
        $accessible_class_ids = array_column($accessible_classes, 'id');

        $can_access_all_classes_flag = ($current_user['can_access_all_classes'] ?? 0) == 1;

        if ($can_access_all_classes_flag) {
            $stats = [
                'classes' => $this->getClassesCount(),
                'subjects' => $this->getSubjectsCount(),
                'syllabi' => $this->getSyllabiCount(),
                'lectures' => $this->getLecturesCount()
            ];
        } else {
            $stats = [
                'classes' => count($accessible_classes),
                'subjects' => $this->getSubjectsCountByClassIds($accessible_class_ids),
                'syllabi' => $this->getSyllabiCountByClassIds($accessible_class_ids),
                'lectures' => $this->getLecturesCountByClassIds($accessible_class_ids)
            ];
        }

        $stats['widget_data'] = [
            [
                'title' => 'Classes',
                'count' => $stats['classes'],
                'icon' => 'ph-fill ph-chalkboard-teacher',
                'color' => '#3D7FF9',
                'chart_id' => 'classes-chart',
                'image' => 'assets/images/dashboard/courses.jpeg'
            ],
            [
                'title' => 'Subjects',
                'count' => $stats['subjects'],
                'icon' => 'ph-fill ph-book-open',
                'color' => '#27CFA7',
                'chart_id' => 'subjects-chart',
                'image' => 'assets/images/dashboard/elearning.jpeg'
            ],
            [
                'title' => 'Syllabi',
                'count' => $stats['syllabi'],
                'icon' => 'ph-fill ph-list',
                'color' => '#6142FF',
                'chart_id' => 'syllabi-chart',
                'image' => 'assets/images/dashboard/courses.jpeg'
            ],
            [
                'title' => 'Lectures',
                'count' => $stats['lectures'],
                'icon' => 'ph-fill ph-play-circle',
                'color' => '#FA902F',
                'chart_id' => 'lectures-chart',
                'image' => 'assets/images/dashboard/elearning.jpeg'
            ]
        ];

        $stats['radial_chart_data'] = [
            'values' => [$stats['classes'], $stats['subjects'], $stats['lectures']],
            'labels' => ['Classes', 'Subjects', 'Lectures'],
            'colors' => ['#3D7FF9', '#27CFA7', '#020203'],
            'total' => $stats['classes'] + $stats['subjects'] + $stats['lectures']
        ];

        $stats['activity_stats'] = $this->getActivityStats($current_user);

        return $stats;
    }

    private function getSoloStudentStats($current_user)
    {
        // Solo students see their accessible classes
        require_once 'classes/User.php';
        $user = new User($this->db);
        $accessible_classes = $user->getAccessibleClasses($current_user);
        $accessible_class_ids = array_column($accessible_classes, 'id');

        $stats = [
            'accessible_classes' => count($accessible_classes),
            'subjects' => empty($accessible_class_ids) ? 0 : $this->getSubjectsCountByClassIds($accessible_class_ids),
            'syllabi' => empty($accessible_class_ids) ? 0 : $this->getSyllabiCountByClassIds($accessible_class_ids),
            'lectures' => empty($accessible_class_ids) ? 0 : $this->getLecturesCountByClassIds($accessible_class_ids),
            'available_classes' => empty($accessible_classes) ? $this->getClassesCount() : 0
        ];

        if (empty($accessible_classes)) {
            // Student has no assigned classes - show available classes
            $stats['widget_data'] = [
                [
                    'title' => 'Available Classes',
                    'count' => $stats['available_classes'],
                    'icon' => 'ph-fill ph-chalkboard-teacher',
                    'color' => '#3D7FF9',
                    'chart_id' => 'available-classes-chart',
                    'image' => 'assets/images/dashboard/courses.jpeg'
                ]
            ];
        } else {
            // Student has assigned classes
            $stats['widget_data'] = [
                [
                    'title' => 'My Classes',
                    'count' => $stats['accessible_classes'],
                    'icon' => 'ph-fill ph-chalkboard-teacher',
                    'color' => '#3D7FF9',
                    'chart_id' => 'classes-chart',
                    'image' => 'assets/images/dashboard/courses.jpeg'
                ],
                [
                    'title' => 'Subjects',
                    'count' => $stats['subjects'],
                    'icon' => 'ph-fill ph-book-open',
                    'color' => '#27CFA7',
                    'chart_id' => 'subjects-chart',
                    'image' => 'assets/images/dashboard/elearning.jpeg'
                ],
                [
                    'title' => 'Syllabi',
                    'count' => $stats['syllabi'],
                    'icon' => 'ph-fill ph-list',
                    'color' => '#6142FF',
                    'chart_id' => 'syllabi-chart',
                    'image' => 'assets/images/dashboard/courses.jpeg'
                ],
                [
                    'title' => 'Lectures',
                    'count' => $stats['lectures'],
                    'icon' => 'ph-fill ph-play-circle',
                    'color' => '#FA902F',
                    'chart_id' => 'lectures-chart',
                    'image' => 'assets/images/dashboard/elearning.jpeg'
                ]
            ];

            $stats['radial_chart_data'] = [
                'values' => [$stats['accessible_classes'], $stats['subjects'], $stats['lectures']],
                'labels' => ['Classes', 'Subjects', 'Lectures'],
                'colors' => ['#3D7FF9', '#27CFA7', '#020203'],
                'total' => $stats['accessible_classes'] + $stats['subjects'] + $stats['lectures']
            ];
        }

        $stats['activity_stats'] = $this->getActivityStats($current_user);

        return $stats;
    }

    private function getDefaultStats()
    {
        return [
            'widget_data' => [],
            'radial_chart_data' => [
                'values' => [0, 0, 0],
                'labels' => ['N/A', 'N/A', 'N/A'],
                'colors' => ['#3D7FF9', '#27CFA7', '#020203'],
                'total' => 0
            ]
        ];
    }

    // Count methods
    public function getActivityStats($current_user)
    {
        $role_name = $current_user['role_name'];
        $currentYear = date('Y');
        $series1 = array_fill(1, 12, 0);
        $series2 = array_fill(1, 12, 0);
        $label1 = '';
        $label2 = '';

        try {
            if ($role_name === 'Super Admin') {
                $label1 = 'Organizations';
                $label2 = 'Schools';
                
                // Organizations
                $query = "SELECT MONTH(created_at) as month, COUNT(*) as count FROM organizations WHERE YEAR(created_at) = ? AND status = 'active' GROUP BY MONTH(created_at)";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$currentYear]);
                while ($row = $stmt->fetch()) $series1[(int)$row['month']] = (int)$row['count'];

                // Schools
                $query = "SELECT MONTH(created_at) as month, COUNT(*) as count FROM schools WHERE YEAR(created_at) = ? AND status = 'active' GROUP BY MONTH(created_at)";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$currentYear]);
                while ($row = $stmt->fetch()) $series2[(int)$row['month']] = (int)$row['count'];

            } elseif ($role_name === 'Organization Admin') {
                $label1 = 'Schools';
                $label2 = 'Users';
                $org_id = $current_user['organization_id'];

                // Schools in Org
                $query = "SELECT MONTH(created_at) as month, COUNT(*) as count FROM schools WHERE organization_id = ? AND YEAR(created_at) = ? AND status = 'active' GROUP BY MONTH(created_at)";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$org_id, $currentYear]);
                while ($row = $stmt->fetch()) $series1[(int)$row['month']] = (int)$row['count'];

                // Users in Org
                $query = "SELECT MONTH(created_at) as month, COUNT(*) as count FROM users WHERE organization_id = ? AND YEAR(created_at) = ? AND status = 'active' GROUP BY MONTH(created_at)";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$org_id, $currentYear]);
                while ($row = $stmt->fetch()) $series2[(int)$row['month']] = (int)$row['count'];

            } elseif ($role_name === 'School Admin') {
                $label1 = 'Users';
                $label2 = 'Classes';
                $school_id = $current_user['school_id'];

                // Users in School
                $query = "SELECT MONTH(created_at) as month, COUNT(*) as count FROM users WHERE school_id = ? AND YEAR(created_at) = ? AND status = 'active' GROUP BY MONTH(created_at)";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$school_id, $currentYear]);
                while ($row = $stmt->fetch()) $series1[(int)$row['month']] = (int)$row['count'];

                // Classes in School - Wait, classes don't have school_id directly in some schemas, but let's check.
                // Assuming classes table has school_id or organization_id. 
                // Let's check classes table structure if possible, but for now I'll use a generic query.
                // If classes are linked via users or something else, this might need adjustment.
                // For simplicity, let's just use 0 if not sure.
                $series2 = array_fill(1, 12, 0); 

            } else {
                // Default/Teacher/Student
                $label1 = 'Activities';
                $label2 = '';
                $series1 = array_fill(1, 12, 0);
                $series2 = array_fill(1, 12, 0);
            }

            return [
                'series' => [
                    [
                        'name' => $label1,
                        'data' => array_values($series1)
                    ],
                    [
                        'name' => $label2,
                        'data' => array_values($series2)
                    ]
                ]
            ];
        } catch (PDOException $e) {
            error_log("Error getting activity stats: " . $e->getMessage());
            return [
                'series' => [
                    ['name' => 'N/A', 'data' => array_fill(0, 12, 0)],
                    ['name' => 'N/A', 'data' => array_fill(0, 12, 0)]
                ]
            ];
        }
    }

    private function getOrganizationsCount()
    {
        try {
            $query = "SELECT COUNT(*) as total FROM organizations WHERE status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting organizations count: " . $e->getMessage());
            return 0;
        }
    }

    private function getSchoolsCount()
    {
        try {
            $query = "SELECT COUNT(*) as total FROM schools WHERE status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting schools count: " . $e->getMessage());
            return 0;
        }
    }

    private function getSchoolsCountByOrganization($organization_id)
    {
        try {
            $query = "SELECT COUNT(*) as total FROM schools WHERE organization_id = ? AND status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$organization_id]);
            $row = $stmt->fetch();
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting schools count by organization: " . $e->getMessage());
            return 0;
        }
    }

    private function getUsersCount()
    {
        try {
            $query = "SELECT COUNT(*) as total FROM users WHERE status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting users count: " . $e->getMessage());
            return 0;
        }
    }

    private function getUsersCountByOrganization($organization_id)
    {
        try {
            $query = "SELECT COUNT(*) as total FROM users WHERE organization_id = ? AND status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$organization_id]);
            $row = $stmt->fetch();
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting users count by organization: " . $e->getMessage());
            return 0;
        }
    }

    private function getUsersCountBySchool($school_id)
    {
        try {
            $query = "SELECT COUNT(*) as total FROM users WHERE school_id = ? AND status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$school_id]);
            $row = $stmt->fetch();
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting users count by school: " . $e->getMessage());
            return 0;
        }
    }

    private function getClassesCount()
    {
        try {
            $query = "SELECT COUNT(*) as total FROM classes WHERE status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting classes count: " . $e->getMessage());
            return 0;
        }
    }

    private function getSubjectsCount()
    {
        try {
            $query = "SELECT COUNT(*) as total FROM subjects WHERE status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting subjects count: " . $e->getMessage());
            return 0;
        }
    }

    private function getSubjectsCountByClassIds($class_ids)
    {
        if (empty($class_ids)) {
            return 0;
        }
        try {
            $placeholders = implode(',', array_fill(0, count($class_ids), '?'));
            $query = "SELECT COUNT(*) as total FROM subjects WHERE class_id IN (" . $placeholders . ") AND status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute($class_ids);
            $row = $stmt->fetch();
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting subjects count by class IDs: " . $e->getMessage());
            return 0;
        }
    }

    private function getSyllabiCount()
    {
        try {
            $query = "SELECT COUNT(*) as total FROM syllabi WHERE status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting syllabi count: " . $e->getMessage());
            return 0;
        }
    }

    private function getSyllabiCountByClassIds($class_ids)
    {
        if (empty($class_ids)) {
            return 0;
        }
        try {
            $placeholders = implode(',', array_fill(0, count($class_ids), '?'));
            $query = "SELECT COUNT(DISTINCT s.id) as total 
                 FROM syllabi s 
                 LEFT JOIN subjects sub ON s.subject_id = sub.id
                 WHERE s.status = 'active'
                 AND (
                     (s.subject_id IS NOT NULL AND sub.class_id IN (" . $placeholders . "))
                     OR
                     (s.class_id IN (" . $placeholders . "))
                 )";
            $stmt = $this->db->prepare($query);
            // Duplicate the class_ids array for both placeholders
            $params = array_merge($class_ids, $class_ids);
            $stmt->execute($params);
            $row = $stmt->fetch();
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting syllabi count by class IDs: " . $e->getMessage());
            return 0;
        }
    }

    private function getLecturesCount()
    {
        try {
            $query = "SELECT COUNT(*) as total FROM lectures WHERE status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting lectures count: " . $e->getMessage());
            return 0;
        }
    }

    private function getLecturesCountByClassIds($class_ids)
    {
        if (empty($class_ids)) {
            return 0;
        }
        try {
            $placeholders = implode(',', array_fill(0, count($class_ids), '?'));
            $query = "SELECT COUNT(DISTINCT l.id) as total 
                 FROM lectures l 
                 JOIN syllabi sy ON l.syllabus_id = sy.id
                 LEFT JOIN subjects s ON sy.subject_id = s.id
                 WHERE l.status = 'active' AND sy.status = 'active'
                 AND (
                     (sy.subject_id IS NOT NULL AND s.class_id IN (" . $placeholders . "))
                     OR
                     (sy.class_id IN (" . $placeholders . "))
                 )";
            $stmt = $this->db->prepare($query);
            // Duplicate the class_ids array for both placeholders
            $params = array_merge($class_ids, $class_ids);
            $stmt->execute($params);
            $row = $stmt->fetch();
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting lectures count by class IDs: " . $e->getMessage());
            return 0;
        }
    }
    private function getZoomMeetingsCount()
    {
        try {
            $query = "SELECT COUNT(*) as total FROM zoom_meetings WHERE status != 'cancelled'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting zoom meetings count: " . $e->getMessage());
            return 0;
        }
    }

    private function getUpcomingMeetingsCount()
    {
        try {
            $query = "SELECT COUNT(*) as total FROM zoom_meetings 
                  WHERE status = 'scheduled' AND scheduled_date > NOW()";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting upcoming meetings count: " . $e->getMessage());
            return 0;
        }
    }

    public function getOverviewBarChartData()
    {
        $organizations = $this->getOrganizationsCount();
        $schools = $this->getSchoolsCount();
        $students = $this->getStudentsCount();

        return [
            'labels' => ['Organizations', 'Schools', 'Students'],
            'data' => [$organizations, $schools, $students],
            'colors' => ['#3D7FF9', '#27CFA7', '#6142FF']
        ];
    }

    private function getStudentsCount()
    {
        try {
            $query = "SELECT COUNT(*) as total FROM users u 
                      JOIN roles r ON u.role_id = r.id 
                      WHERE r.name IN ('Solo Student', 'Student') AND u.status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting students count: " . $e->getMessage());
            return 0;
        }
    }
}
