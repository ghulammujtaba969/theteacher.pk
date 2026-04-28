<!-- Teacher Dashboard Content -->
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

        <!-- Teaching Activity Chart Start -->
        <div class="card mt-24">
            <div class="card-body">
                <div class="mb-20 flex-between flex-wrap gap-8">
                    <h4 class="mb-0">My Teaching Activity</h4>
                    <div class="flex-align gap-16 flex-wrap">
                        <div class="flex-align flex-wrap gap-16">
                            <?php if (isset($stats['activity_stats']['series'])): ?>
                                <?php foreach ($stats['activity_stats']['series'] as $index => $series): ?>
                                    <div class="flex-align flex-wrap gap-8">
                                        <span class="w-8 h-8 rounded-circle <?php echo $index === 0 ? 'bg-main-600' : 'bg-main-two-600'; ?>"></span>
                                        <span class="text-13 text-gray-600"><?php echo htmlspecialchars($series['name']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="flex-align flex-wrap gap-8">
                                    <span class="w-8 h-8 rounded-circle bg-main-600"></span>
                                    <span class="text-13 text-gray-600">Classes</span>
                                </div>
                                <div class="flex-align flex-wrap gap-8">
                                    <span class="w-8 h-8 rounded-circle bg-main-two-600"></span>
                                    <span class="text-13 text-gray-600">Lectures</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <select class="form-select form-control text-13 px-8 pe-24 py-8 rounded-8 w-auto">
                            <option value="1">Yearly</option>
                            <option value="1">Monthly</option>
                            <option value="1">Weekly</option>
                            <option value="1">Today</option>
                        </select>
                    </div>
                </div>
                
                <div id="doubleLineChart" class="tooltip-style y-value-left"></div>
                
            </div>
        </div>
        <!-- Teaching Activity Chart End -->

        <!-- My Classes Start -->
        <div class="card mt-24">
            <div class="card-body">
                <div class="mb-20 flex-between flex-wrap gap-8">
                    <h4 class="mb-0">My Accessible Classes</h4>
                    <a href="classes.php" class="text-13 fw-medium text-main-600 hover-text-decoration-underline">View All</a>
                </div>
                
                <div class="row g-20">
                    <?php if (!empty($accessible_classes_raw)): ?>
                        <?php foreach (array_slice($accessible_classes_raw, 0, 6) as $class): ?>
                        <div class="col-lg-4 col-sm-6">
                            <div class="card border border-gray-100">
                                <div class="card-body p-16">
                                    <div class="flex-align gap-8 mb-16">
                                        <span class="w-40 h-40 bg-main-600 text-white rounded-circle flex-center">
                                            <i class="ph ph-chalkboard-teacher"></i>
                                        </span>
                                        <div>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($class['class_name']); ?></h6>
                                            <span class="text-13 text-gray-400"><?php echo htmlspecialchars($class['class_code']); ?></span>
                                        </div>
                                    </div>
                                    <p class="text-13 text-gray-600 mb-16"><?php echo htmlspecialchars(substr($class['description'] ?? '', 0, 80)) . (strlen($class['description'] ?? '') > 80 ? '...' : ''); ?></p>
                                    <div class="flex-between gap-8">
                                        <small class="text-gray-400">Created: <?php echo date('M d, Y', strtotime($class['created_at'])); ?></small>
                                        <a href="classes.php?id=<?php echo $class['id']; ?>" class="btn btn-outline-main btn-sm rounded-pill">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h5>No Classes Assigned</h5>
                                <p>You don't have access to any classes yet. Please contact your administrator to assign classes to you.</p>
                                <a href="profile.php" class="btn btn-main mt-3">Update Profile</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- My Classes End -->

        <!-- Quick Actions Start -->
        <?php if (!empty($accessible_classes_raw)): ?>
        <div class="card mt-24">
            <div class="card-body">
                <div class="mb-20 flex-between flex-wrap gap-8">
                    <h4 class="mb-0">Quick Actions</h4>
                </div>
                
                <div class="row g-20">
                    <div class="col-lg-3 col-sm-6">
                        <a href="profile.php" class="btn btn-main w-100 rounded-pill py-12">
                            <i class="ph ph-user-circle me-2"></i>Update Profile
                        </a>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <a href="classes.php" class="btn btn-outline-main w-100 rounded-pill py-12">
                            <i class="ph ph-chalkboard-teacher me-2"></i>My Classes
                        </a>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <a href="lectures.php" class="btn btn-outline-main w-100 rounded-pill py-12">
                            <i class="ph ph-books me-2"></i>My Lectures
                        </a>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <a href="classes.php" class="btn btn-outline-main w-100 rounded-pill py-12">
                            <i class="ph ph-eye me-2"></i>View Classes
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
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
        
        <!-- Teaching Activities Start -->
        <div class="card mt-24">
            <div class="card-body">
                <div class="mb-20 flex-between flex-wrap gap-8">
                    <h4 class="mb-0">My Teaching</h4>
                    <a href="lectures.php" class="text-13 fw-medium text-main-600 hover-text-decoration-underline">View All Lectures</a>
                </div>
                
                <div class="p-xl-4 py-16 px-12 flex-between gap-8 rounded-8 border border-gray-100 hover-border-gray-200 transition-1 mb-16">
                    <div class="flex-align flex-wrap gap-8">
                        <span class="text-main-600 bg-main-50 w-44 h-44 rounded-circle flex-center text-2xl flex-shrink-0"><i class="ph-fill ph-chalkboard-teacher"></i></span>
                        <div>
                            <h6 class="mb-0">My Classes</h6>
                            <span class="text-13 text-gray-400"><?php echo $stats['classes']; ?> accessible classes</span>
                        </div>
                    </div>
                    <a href="classes.php" class="text-gray-900 hover-text-main-600"><i class="ph ph-caret-right"></i></a>
                </div>
                
                <div class="p-xl-4 py-16 px-12 flex-between gap-8 rounded-8 border border-gray-100 hover-border-gray-200 transition-1 mb-16">
                    <div class="flex-align flex-wrap gap-8">
                        <span class="text-main-600 bg-main-50 w-44 h-44 rounded-circle flex-center text-2xl flex-shrink-0"><i class="ph ph-book-open"></i></span>
                        <div>
                            <h6 class="mb-0">Subjects</h6>
                            <span class="text-13 text-gray-400"><?php echo $stats['subjects']; ?> available subjects</span>
                        </div>
                    </div>
                    <a href="subjects.php" class="text-gray-900 hover-text-main-600"><i class="ph ph-caret-right"></i></a>
                </div>
                
                <div class="p-xl-4 py-16 px-12 flex-between gap-8 rounded-8 border border-gray-100 hover-border-gray-200 transition-1">
                    <div class="flex-align flex-wrap gap-8">
                        <span class="text-main-600 bg-main-50 w-44 h-44 rounded-circle flex-center text-2xl flex-shrink-0"><i class="ph ph-play-circle"></i></span>
                        <div>
                            <h6 class="mb-0">Lectures</h6>
                            <span class="text-13 text-gray-400"><?php echo $stats['lectures']; ?> total lectures</span>
                        </div>
                    </div>
                    <a href="lectures.php" class="text-gray-900 hover-text-main-600"><i class="ph ph-caret-right"></i></a>
                </div>
            </div>
        </div>
        <!-- Teaching Activities End -->
        
    </div>

</div>
