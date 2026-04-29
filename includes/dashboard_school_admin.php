<!-- School Admin Dashboard Content -->
<div class="row gy-4">
    <div class="col-lg-9">
        <!-- Widgets Start -->
        <div class="row gy-4">
            <?php foreach ($stats['widget_data'] as $widget): ?>
            <div class="col-xxl-3 col-sm-6">
                <div class="card" style="background-image: url('<?php echo $widget['image']; ?>'); background-size: cover; background-position: center; position: relative;">
                    <div class="card-body" style="background: rgba(0, 0, 0, 0.4); border-radius: inherit;">
                        <h4 class="mb-2 text-white"><?php echo number_format($widget['count']); ?>+</h4>
                        <span class="text-white opacity-75"><?php echo $widget['title']; ?></span>
                        <div class="flex-between gap-8 mt-16">
                            <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle text-white text-2xl" style="background-color: <?php echo $widget['color']; ?>">
                                <i class="<?php echo $widget['icon']; ?>"></i>
                            </span>
                            <div id="<?php echo $widget['chart_id']; ?>" class="remove-tooltip-title rounded-tooltip-value"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <!-- Widgets End -->

        <!-- School Users Start -->
        <div class="card mt-24">
            <div class="card-body">
                <div class="mb-20 flex-between flex-wrap gap-8">
                    <h4 class="mb-0">School Users</h4>
                    <a href="users.php" class="text-13 fw-medium text-main-600 hover-text-decoration-underline">View All</a>
                </div>
                
                <?php 
                try {
                    $school_users_query = "SELECT u.*, r.name as role_name FROM users u 
                                          JOIN roles r ON u.role_id = r.id 
                                          WHERE u.school_id = ? AND u.status = 'active' 
                                          ORDER BY u.created_at DESC LIMIT 6";
                    $school_users_stmt = $pdo->prepare($school_users_query);
                    $school_users_stmt->execute([$current_user['school_id']]);
                    $school_users = $school_users_stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (Exception $e) {
                    $school_users = [];
                }
                ?>
                
                <div class="row g-20">
                    <?php if (!empty($school_users)): ?>
                        <?php foreach ($school_users as $school_user): ?>
                        <div class="col-lg-4 col-sm-6">
                            <div class="card border border-gray-100">
                                <div class="card-body p-16">
                                    <div class="flex-align gap-8 mb-16">
                                        <span class="w-40 h-40 bg-main-600 text-white rounded-circle flex-center">
                                            <i class="ph ph-user"></i>
                                        </span>
                                        <div>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($school_user['full_name'] ?? $school_user['username']); ?></h6>
                                            <span class="text-13 text-gray-400"><?php echo htmlspecialchars($school_user['role_name']); ?></span>
                                        </div>
                                    </div>
                                    <p class="text-13 text-gray-600 mb-16"><?php echo htmlspecialchars($school_user['email']); ?></p>
                                    <div class="flex-between gap-8">
                                        <small class="text-gray-400">Joined: <?php echo date('M d, Y', strtotime($school_user['created_at'])); ?></small>
                                        <a href="users.php?id=<?php echo $school_user['id']; ?>" class="btn btn-outline-main btn-sm rounded-pill">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <p class="text-center text-gray-500">No users found in your school.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- School Users End -->

        <!-- Quick Actions Start -->
        <div class="card mt-24">
            <div class="card-body">
                <div class="mb-20 flex-between flex-wrap gap-8">
                    <h4 class="mb-0">Quick Actions</h4>
                </div>
                
                <div class="row g-20">
                    <div class="col-lg-3 col-sm-6">
                        <a href="users.php?action=add&role=teacher" class="btn btn-main w-100 rounded-pill py-12">
                            <i class="ph ph-chalkboard-teacher me-2"></i>Add Teacher
                        </a>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <a href="users.php?action=add&role=solo_student" class="btn btn-outline-main w-100 rounded-pill py-12">
                            <i class="ph ph-student me-2"></i>Add Student
                        </a>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <a href="class-access.php" class="btn btn-outline-main w-100 rounded-pill py-12">
                            <i class="ph ph-key me-2"></i>Manage Access
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Quick Actions End -->
    </div>

    <div class="col-lg-3">
        <!-- Calendar Start -->
        <div class="card">
            <div class="card-body">
                <div class="calendar">
                    <div class="calendar__header">
                        <button type="button" class="calendar__arrow left"><i class="ph ph-caret-left"></i></button>
                        <p class="display h6 mb-0">""</p>
                        <button type="button" class="calendar__arrow right"><i class="ph ph-caret-right"></i></button>
                    </div>
                
                    <div class="calendar__week week">
                        <div class="calendar__week-text">Su</div>
                        <div class="calendar__week-text">Mo</div>
                        <div class="calendar__week-text">Tu</div>
                        <div class="calendar__week-text">We</div>
                        <div class="calendar__week-text">Th</div>
                        <div class="calendar__week-text">Fr</div>
                        <div class="calendar__week-text">Sa</div>
                    </div>
                    <div class="days"></div>
                </div>
            </div>
        </div>
        <!-- Calendar End -->
        
        <!-- School Activities Start -->
        <div class="card mt-24">
            <div class="card-body">
                <div class="mb-20 flex-between flex-wrap gap-8">
                    <h4 class="mb-0">School Activities</h4>
                    <a href="classes.php" class="text-13 fw-medium text-main-600 hover-text-decoration-underline">View All Classes</a>
                </div>
                
                <div class="p-xl-4 py-16 px-12 flex-between gap-8 rounded-8 border border-gray-100 hover-border-gray-200 transition-1 mb-16">
                    <div class="flex-align flex-wrap gap-8">
                        <span class="text-main-600 bg-main-50 w-44 h-44 rounded-circle flex-center text-2xl flex-shrink-0"><i class="ph-fill ph-users-three"></i></span>
                        <div>
                            <h6 class="mb-0">School Users</h6>
                            <span class="text-13 text-gray-400"><?php echo $stats['users']; ?> users in school</span>
                        </div>
                    </div>
                    <a href="users.php" class="text-gray-900 hover-text-main-600"><i class="ph ph-caret-right"></i></a>
                </div>
                
                <div class="p-xl-4 py-16 px-12 flex-between gap-8 rounded-8 border border-gray-100 hover-border-gray-200 transition-1 mb-16">
                    <div class="flex-align flex-wrap gap-8">
                        <span class="text-main-600 bg-main-50 w-44 h-44 rounded-circle flex-center text-2xl flex-shrink-0"><i class="ph ph-chalkboard-teacher"></i></span>
                        <div>
                            <h6 class="mb-0">Classes</h6>
                            <span class="text-13 text-gray-400"><?php echo $stats['classes']; ?> available classes</span>
                        </div>
                    </div>
                    <a href="classes.php" class="text-gray-900 hover-text-main-600"><i class="ph ph-caret-right"></i></a>
                </div>
                
                <div class="p-xl-4 py-16 px-12 flex-between gap-8 rounded-8 border border-gray-100 hover-border-gray-200 transition-1">
                    <div class="flex-align flex-wrap gap-8">
                        <span class="text-main-600 bg-main-50 w-44 h-44 rounded-circle flex-center text-2xl flex-shrink-0"><i class="ph ph-book-open"></i></span>
                        <div>
                            <h6 class="mb-0">Subjects</h6>
                            <span class="text-13 text-gray-400"><?php echo $stats['subjects']; ?> available subjects</span>
                        </div>
                    </div>
                    <a href="subjects.php" class="text-gray-900 hover-text-main-600"><i class="ph ph-caret-right"></i></a>
                </div>
            </div>
        </div>
        <!-- School Activities End -->
        
    </div>

</div>
