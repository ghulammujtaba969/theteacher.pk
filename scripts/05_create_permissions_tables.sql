-- ============================================================
-- theteacher.pk - Comprehensive Roles & Permissions Migration
-- ============================================================

-- Step 1: Create permissions table
CREATE TABLE IF NOT EXISTS `permissions` (
    `id`           INT(11) NOT NULL AUTO_INCREMENT,
    `name`         VARCHAR(100) NOT NULL COMMENT 'Dot-notation slug e.g. classes.create',
    `display_name` VARCHAR(150) NOT NULL,
    `description`  TEXT,
    `module`       VARCHAR(50)  NOT NULL COMMENT 'Grouping e.g. classes, users, batches',
    `sort_order`   INT(11)      NOT NULL DEFAULT 0,
    `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_permission_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 2: Role-Permission pivot
CREATE TABLE IF NOT EXISTS `role_permissions` (
    `id`            INT(11) NOT NULL AUTO_INCREMENT,
    `role_id`       INT(11) NOT NULL,
    `permission_id` INT(11) NOT NULL,
    `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_role_permission` (`role_id`, `permission_id`),
    CONSTRAINT `fk_rp_role`       FOREIGN KEY (`role_id`)       REFERENCES `roles`(`id`)       ON DELETE CASCADE,
    CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 3: Direct user-level permission overrides (grant or deny on top of role)
CREATE TABLE IF NOT EXISTS `user_permissions` (
    `id`            INT(11)     NOT NULL AUTO_INCREMENT,
    `user_id`       INT(11)     NOT NULL,
    `permission_id` INT(11)     NOT NULL,
    `granted`       TINYINT(1)  NOT NULL DEFAULT 1 COMMENT '1=grant, 0=explicit deny',
    `granted_by`    INT(11)     DEFAULT NULL,
    `created_at`    TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_user_permission` (`user_id`, `permission_id`),
    CONSTRAINT `fk_up_user`       FOREIGN KEY (`user_id`)       REFERENCES `users`(`id`)       ON DELETE CASCADE,
    CONSTRAINT `fk_up_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Step 4: Seed all permissions
-- ============================================================

INSERT INTO `permissions` (`name`, `display_name`, `description`, `module`, `sort_order`) VALUES
('dashboard.view', 'View Dashboard', 'Access the main dashboard', 'dashboard', 10),
('classes.view', 'View Classes', 'View class list and details', 'classes', 20),
('classes.create', 'Create Classes', 'Create new classes', 'classes', 21),
('classes.edit', 'Edit Classes', 'Edit existing classes', 'classes', 22),
('classes.delete', 'Delete Classes', 'Delete / deactivate classes', 'classes', 23),
('classes.toggle_registration', 'Toggle Class Registration', 'Open or close registration for a class', 'classes', 24),
('courses.view', 'View Courses', 'View course list and details', 'courses', 30),
('courses.create', 'Create Courses', 'Create new courses', 'courses', 31),
('courses.edit', 'Edit Courses', 'Edit existing courses', 'courses', 32),
('courses.delete', 'Delete Courses', 'Delete / deactivate courses', 'courses', 33),
('courses.toggle_registration', 'Toggle Course Registration', 'Open or close registration for a course', 'courses', 34),
('subjects.view', 'View Subjects', 'View subject list and details', 'subjects', 40),
('subjects.create', 'Create Subjects', 'Create new subjects', 'subjects', 41),
('subjects.edit', 'Edit Subjects', 'Edit existing subjects', 'subjects', 42),
('subjects.delete', 'Delete Subjects', 'Delete / deactivate subjects', 'subjects', 43),
('syllabi.view', 'View Syllabi', 'View syllabus list and details', 'syllabi', 50),
('syllabi.create', 'Create Syllabi', 'Create new syllabi', 'syllabi', 51),
('syllabi.edit', 'Edit Syllabi', 'Edit existing syllabi', 'syllabi', 52),
('syllabi.delete', 'Delete Syllabi', 'Delete / deactivate syllabi', 'syllabi', 53),
('lectures.view', 'View Lectures', 'View lecture list and content', 'lectures', 60),
('lectures.create', 'Create Lectures', 'Create new lectures', 'lectures', 61),
('lectures.edit', 'Edit Lectures', 'Edit existing lectures', 'lectures', 62),
('lectures.delete', 'Delete Lectures', 'Delete / deactivate lectures', 'lectures', 63),
('batches.view', 'View Batches', 'View batch list and details', 'batches', 70),
('batches.create', 'Create Batches', 'Create new batches', 'batches', 71),
('batches.edit', 'Edit Batches', 'Edit existing batches', 'batches', 72),
('batches.delete', 'Delete Batches', 'Delete batches', 'batches', 73),
('batches.manage_students', 'Manage Batch Students', 'Add, remove and move students in batches', 'batches', 74),
('batches.generate_links', 'Generate Registration Links', 'Create shareable batch registration links', 'batches', 75),
('enrollments.view', 'View Enrollments', 'View enrollment requests', 'enrollments', 80),
('enrollments.approve', 'Approve Enrollments', 'Approve pending enrollment requests', 'enrollments', 81),
('enrollments.reject', 'Reject Enrollments', 'Reject pending enrollment requests', 'enrollments', 82),
('enrollments.manage', 'Manage Enrollments', 'Full enrollment management including status changes', 'enrollments', 83),
('enrollments.self_enroll', 'Self Enroll in Batch', 'Student can enroll themselves in open batches', 'enrollments', 84),
('users.view', 'View Users', 'View user list and profiles', 'users', 90),
('users.create', 'Create Users', 'Create new user accounts', 'users', 91),
('users.edit', 'Edit Users', 'Edit existing user accounts', 'users', 92),
('users.delete', 'Delete Users', 'Delete / deactivate user accounts', 'users', 93),
('users.reset_password', 'Reset User Passwords', 'Reset passwords for other users', 'users', 94),
('solo_students.view', 'View Solo Students', 'View solo student list', 'solo_students', 95),
('solo_students.manage', 'Manage Solo Students', 'Full management of solo student accounts', 'solo_students', 96),
('organizations.view', 'View Organizations', 'View organization list and details', 'organizations', 100),
('organizations.create', 'Create Organizations', 'Create new organizations', 'organizations', 101),
('organizations.edit', 'Edit Organizations', 'Edit existing organizations', 'organizations', 102),
('organizations.delete', 'Delete Organizations', 'Delete organizations', 'organizations', 103),
('schools.view', 'View Schools', 'View school list and details', 'schools', 110),
('schools.create', 'Create Schools', 'Create new schools', 'schools', 111),
('schools.edit', 'Edit Schools', 'Edit existing schools', 'schools', 112),
('schools.delete', 'Delete Schools', 'Delete schools', 'schools', 113),
('class_access.view', 'View Class Access', 'View class access permissions list', 'class_access', 120),
('class_access.grant', 'Grant Class Access', 'Grant users access to classes', 'class_access', 121),
('class_access.revoke', 'Revoke Class Access', 'Revoke user access to classes', 'class_access', 122),
('inquiries.view', 'View Inquiries', 'View class registration inquiries', 'inquiries', 130),
('inquiries.approve', 'Approve Inquiries', 'Approve class registration inquiries', 'inquiries', 131),
('inquiries.reject', 'Reject Inquiries', 'Reject class registration inquiries', 'inquiries', 132),
('zoom.view', 'View Zoom Meetings', 'View Zoom meeting list', 'zoom', 140),
('zoom.create', 'Create Zoom Meetings', 'Create new Zoom meetings', 'zoom', 141),
('zoom.edit', 'Edit Zoom Meetings', 'Edit existing Zoom meetings', 'zoom', 142),
('zoom.delete', 'Delete Zoom Meetings', 'Delete Zoom meetings', 'zoom', 143),
('zoom.join', 'Join Zoom Meetings', 'Access Zoom meeting join links', 'zoom', 144),
('pending_registrations.view', 'View Pending Registrations', 'View pending self-registration requests', 'pending_reg', 150),
('pending_registrations.approve', 'Approve Pending Registrations', 'Approve self-registration requests', 'pending_reg', 151),
('pending_registrations.reject', 'Reject Pending Registrations', 'Reject self-registration requests', 'pending_reg', 152),
('pptx.view', 'View PPTX Pages', 'View PPTX viewer page list', 'pptx', 160),
('pptx.manage', 'Manage PPTX Pages', 'Create, edit and delete PPTX viewer pages', 'pptx', 161),
('roles.view', 'View Roles', 'View roles list', 'roles_perms', 170),
('roles.create', 'Create Roles', 'Create new roles', 'roles_perms', 171),
('roles.edit', 'Edit Roles', 'Edit existing roles and assign permissions', 'roles_perms', 172),
('roles.delete', 'Delete Roles', 'Delete roles', 'roles_perms', 173),
('permissions.view', 'View Permissions', 'View all system permissions', 'roles_perms', 174),
('permissions.manage', 'Manage Permissions', 'Assign/revoke permissions on roles and users', 'roles_perms', 175),
('profile.view', 'View Own Profile', 'View own profile page', 'profile', 180),
('profile.edit', 'Edit Own Profile', 'Edit own profile information', 'profile', 181),
('reports.view', 'View Reports', 'Access system reports and analytics', 'reports', 190)
ON DUPLICATE KEY UPDATE
    `display_name` = VALUES(`display_name`),
    `description` = VALUES(`description`),
    `module` = VALUES(`module`),
    `sort_order` = VALUES(`sort_order`);

-- ============================================================
-- Step 5: Assign permissions to the 5 default roles
-- ============================================================

-- Super Admin: all permissions
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id
FROM `roles` r
CROSS JOIN `permissions` p
WHERE r.name = 'Super Admin'
ON DUPLICATE KEY UPDATE `role_id` = `role_id`;

-- Organization Admin
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id
FROM `roles` r
JOIN `permissions` p ON p.name IN (
    'dashboard.view',
    'classes.view',
    'courses.view',
    'subjects.view',
    'syllabi.view',
    'lectures.view',
    'batches.view','batches.create','batches.edit','batches.manage_students','batches.generate_links',
    'enrollments.view','enrollments.approve','enrollments.reject','enrollments.manage',
    'users.view','users.create','users.edit','users.delete','users.reset_password',
    'solo_students.view','solo_students.manage',
    'organizations.view',
    'schools.view','schools.create','schools.edit','schools.delete',
    'class_access.view','class_access.grant','class_access.revoke',
    'inquiries.view','inquiries.approve','inquiries.reject',
    'zoom.view','zoom.create','zoom.edit','zoom.delete','zoom.join',
    'pending_registrations.view','pending_registrations.approve','pending_registrations.reject',
    'profile.view','profile.edit',
    'reports.view'
)
WHERE r.name = 'Organization Admin'
ON DUPLICATE KEY UPDATE `role_id` = `role_id`;

-- School Admin
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id
FROM `roles` r
JOIN `permissions` p ON p.name IN (
    'dashboard.view',
    'classes.view',
    'courses.view',
    'subjects.view',
    'syllabi.view',
    'lectures.view',
    'batches.view','batches.manage_students',
    'enrollments.view','enrollments.approve','enrollments.reject',
    'users.view','users.create','users.edit',
    'class_access.view','class_access.grant','class_access.revoke',
    'inquiries.view','inquiries.approve','inquiries.reject',
    'zoom.view','zoom.join',
    'profile.view','profile.edit'
)
WHERE r.name = 'School Admin'
ON DUPLICATE KEY UPDATE `role_id` = `role_id`;

-- Teacher
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id
FROM `roles` r
JOIN `permissions` p ON p.name IN (
    'dashboard.view',
    'classes.view',
    'courses.view',
    'subjects.view',
    'syllabi.view',
    'lectures.view','lectures.create','lectures.edit',
    'batches.view',
    'enrollments.view',
    'zoom.view','zoom.create','zoom.edit','zoom.join',
    'profile.view','profile.edit'
)
WHERE r.name = 'Teacher'
ON DUPLICATE KEY UPDATE `role_id` = `role_id`;

-- Solo Student
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id
FROM `roles` r
JOIN `permissions` p ON p.name IN (
    'dashboard.view',
    'classes.view',
    'courses.view',
    'syllabi.view',
    'lectures.view',
    'batches.view',
    'enrollments.view','enrollments.self_enroll',
    'zoom.view','zoom.join',
    'profile.view','profile.edit'
)
WHERE r.name = 'Solo Student'
ON DUPLICATE KEY UPDATE `role_id` = `role_id`;
