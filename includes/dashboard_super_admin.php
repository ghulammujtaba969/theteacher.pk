<!-- Super Admin Dashboard Content -->
<div class="row gy-4">
    <div class="col-lg-9">
        <!-- Widgets Start -->
        <div class="row gy-4">
            <?php foreach ($stats['widget_data'] as $widget): ?>
            <div class="col-xxl-6 col-sm-6">
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

        <!-- System Overview Chart Start -->
        <div class="card mt-24">
            <div class="card-body">
                <div class="mb-20 flex-between flex-wrap gap-8">
                    <h4 class="mb-0">System Activity Overview</h4>
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
                                    <span class="text-13 text-gray-600">Organizations</span>
                                </div>
                                <div class="flex-align flex-wrap gap-8">
                                    <span class="w-8 h-8 rounded-circle bg-main-two-600"></span>
                                    <span class="text-13 text-gray-600">Schools</span>
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
        <!-- System Overview Chart End -->

        <!-- Recent Organizations Start -->
        <div class="card mt-24">
            <div class="card-body">
                <div class="mb-20 flex-between flex-wrap gap-8">
                    <h4 class="mb-0">Recent Organizations</h4>
                    <a href="organizations.php" class="text-13 fw-medium text-main-600 hover-text-decoration-underline">View All</a>
                </div>
                
                <?php 
                try {
                    $recent_orgs_query = "SELECT * FROM organizations WHERE status = 'active' ORDER BY created_at DESC LIMIT 3";
                    $recent_orgs_stmt = $pdo->prepare($recent_orgs_query);
                    $recent_orgs_stmt->execute();
                    $recent_organizations = $recent_orgs_stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (Exception $e) {
                    $recent_organizations = [];
                }
                ?>
                
                <div class="row g-20">
                    <?php if (!empty($recent_organizations)): ?>
                        <?php foreach ($recent_organizations as $org): ?>
                        <div class="col-lg-4 col-sm-6">
                            <div class="card border border-gray-100">
                                <div class="card-body p-16">
                                    <div class="flex-align gap-8 mb-16">
                                        <span class="w-40 h-40 bg-main-600 text-white rounded-circle flex-center">
                                            <i class="ph ph-buildings"></i>
                                        </span>
                                        <div>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($org['name']); ?></h6>
                                            <span class="text-13 text-gray-400">Organization</span>
                                        </div>
                                    </div>
                                    <p class="text-13 text-gray-600 mb-16"><?php echo htmlspecialchars(substr($org['description'] ?? '', 0, 80)) . (strlen($org['description'] ?? '') > 80 ? '...' : ''); ?></p>
                                    <div class="flex-between gap-8">
                                        <small class="text-gray-400">Created: <?php echo date('M d, Y', strtotime($org['created_at'])); ?></small>
                                        <a href="organizations.php?id=<?php echo $org['id']; ?>" class="btn btn-outline-main btn-sm rounded-pill">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <p class="text-center text-gray-500">No organizations found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Recent Organizations End -->

        <!-- Quick Actions Start -->
        <div class="card mt-24">
            <div class="card-body">
                <div class="mb-20 flex-between flex-wrap gap-8">
                    <h4 class="mb-0">Quick Actions</h4>
                </div>
                
                <div class="row g-20">
                    <div class="col-lg-3 col-sm-6">
                        <a href="organizations.php?action=add" class="btn btn-main w-100 rounded-pill py-12">
                            <i class="ph ph-plus me-2"></i>Add Organization
                        </a>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <a href="schools.php?action=add" class="btn btn-outline-main w-100 rounded-pill py-12">
                            <i class="ph ph-plus me-2"></i>Add School
                        </a>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <a href="users.php?action=add" class="btn btn-outline-main w-100 rounded-pill py-12">
                            <i class="ph ph-plus me-2"></i>Add User
                        </a>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <a href="classes.php?action=add" class="btn btn-outline-main w-100 rounded-pill py-12">
                            <i class="ph ph-plus me-2"></i>Add Class
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
        
        <!-- Recent Activities Start -->
        <div class="card mt-24">
            <div class="card-body">
                <div class="mb-20 flex-between flex-wrap gap-8">
                    <h4 class="mb-0">System Activities</h4>
                    <a href="pending_registrations.php" class="text-13 fw-medium text-main-600 hover-text-decoration-underline">Pending Registrations</a>
                </div>
                
                <?php 
                try {
                    $pending_count_query = "SELECT COUNT(*) as count FROM pending_users WHERE status = 'pending'";
                    $pending_count_stmt = $pdo->prepare($pending_count_query);
                    $pending_count_stmt->execute();
                    $pending_count = $pending_count_stmt->fetch()['count'];
                } catch (Exception $e) {
                    $pending_count = 0;
                }
                ?>
                
                <div class="p-xl-4 py-16 px-12 flex-between gap-8 rounded-8 border border-gray-100 hover-border-gray-200 transition-1 mb-16">
                    <div class="flex-align flex-wrap gap-8">
                        <span class="text-main-600 bg-main-50 w-44 h-44 rounded-circle flex-center text-2xl flex-shrink-0"><i class="ph-fill ph-user-plus"></i></span>
                        <div>
                            <h6 class="mb-0">Pending Registrations</h6>
                            <span class="text-13 text-gray-400"><?php echo $pending_count; ?> pending approval</span>
                        </div>
                    </div>
                    <a href="pending_registrations.php" class="text-gray-900 hover-text-main-600"><i class="ph ph-caret-right"></i></a>
                </div>
                
                <div class="p-xl-4 py-16 px-12 flex-between gap-8 rounded-8 border border-gray-100 hover-border-gray-200 transition-1 mb-16">
                    <div class="flex-align flex-wrap gap-8">
                        <span class="text-main-600 bg-main-50 w-44 h-44 rounded-circle flex-center text-2xl flex-shrink-0"><i class="ph ph-buildings"></i></span>
                        <div>
                            <h6 class="mb-0">Total Organizations</h6>
                            <span class="text-13 text-gray-400"><?php echo $stats['organizations']; ?> active organizations</span>
                        </div>
                    </div>
                    <a href="organizations.php" class="text-gray-900 hover-text-main-600"><i class="ph ph-caret-right"></i></a>
                </div>
                
                <div class="p-xl-4 py-16 px-12 flex-between gap-8 rounded-8 border border-gray-100 hover-border-gray-200 transition-1">
                    <div class="flex-align flex-wrap gap-8">
                        <span class="text-main-600 bg-main-50 w-44 h-44 rounded-circle flex-center text-2xl flex-shrink-0"><i class="ph ph-graduation-cap"></i></span>
                        <div>
                            <h6 class="mb-0">Total Schools</h6>
                            <span class="text-13 text-gray-400"><?php echo $stats['schools']; ?> active schools</span>
                        </div>
                    </div>
                    <a href="schools.php" class="text-gray-900 hover-text-main-600"><i class="ph ph-caret-right"></i></a>
                </div>
            </div>
        </div>
        <!-- Recent Activities End -->
        
        <!-- Progress Bar Start -->
        <div class="card mt-24">
            <div class="card-header border-bottom border-gray-100">
                <h5 class="mb-0">System Overview</h5>
            </div>
            <div class="card-body">
               <div id="radialMultipleBar"></div>

               <div class="">
                    <h6 class="text-lg mb-16 text-center"> <span class="text-gray-400">Total Content:</span> <?php echo $stats['radial_chart_data']['total']; ?> items</h6>
                    <div class="flex-between gap-8 flex-wrap">
                        <div class="flex-align flex-column">
                            <h6 class="mb-6"><?php echo $stats['classes']; ?></h6>
                            <span class="w-30 h-3 rounded-pill bg-main-600"></span>
                            <span class="text-13 mt-6 text-gray-600">Classes</span>
                        </div>
                        <div class="flex-align flex-column">
                            <h6 class="mb-6"><?php echo $stats['subjects']; ?></h6>
                            <span class="w-30 h-3 rounded-pill bg-main-two-600"></span>
                            <span class="text-13 mt-6 text-gray-600">Subjects</span>
                        </div>
                        <div class="flex-align flex-column">
                            <h6 class="mb-6"><?php echo $stats['lectures']; ?></h6>
                            <span class="w-30 h-3 rounded-pill bg-gray-500"></span>
                            <span class="text-13 mt-6 text-gray-600">Lectures</span>
                        </div>
                    </div>
               </div>
            </div>
        </div>
        <!-- Progress bar end -->
    </div>

</div>