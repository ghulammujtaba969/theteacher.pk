<?php
// includes/dashboard_solo_student.php (Updated version)
$database = new Database();
$db = $database->getConnection();

// Include ClassInquiry class
require_once 'classes/ClassInquiry.php';
$classInquiry = new ClassInquiry($db);

// Get user's inquiries
$userInquiriesStmt = $classInquiry->getUserInquiries($current_user['id']);
$userInquiries = $userInquiriesStmt->fetchAll(PDO::FETCH_ASSOC);

// Get approved/access classes (explicit permissions)
$approvedClasses = $user->getAccessibleClasses($current_user);

// Also include classes from active enrollments (via batches)
require_once 'classes/BatchEnrollment.php';
require_once 'classes/ClassModel.php';
$enrollmentModel = new BatchEnrollment($db);
$classModel = new ClassModel($db);

$enrollments = $enrollmentModel->getEnrollmentsByUser($current_user['id']);
$enrolled_class_ids = [];
foreach ($enrollments as $en) {
    if (!empty($en['class_id'])) {
        $enrolled_class_ids[] = (int)$en['class_id'];
    }
}
$enrolled_class_ids = array_unique($enrolled_class_ids);

// Fetch full class records for enrolled classes
$enrolled_classes = [];
if (!empty($enrolled_class_ids)) {
    $stmt = $classModel->read($enrolled_class_ids, null, false);
    $enrolled_classes = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
}

// Merge accessible + enrolled classes (unique by id)
$combined = [];
foreach ($approvedClasses as $c) { if (!empty($c['id'])) { $combined[$c['id']] = $c; } }
foreach ($enrolled_classes as $c) { if (!empty($c['id'])) { $combined[$c['id']] = $c; } }
$approvedClasses = array_values($combined);
?>

<!-- Solo Student Dashboard -->
<div class="row">
    <!-- Welcome Card -->
    <div class="col-xxl-9 mb-20">
        <div class="card h-100">
            <div class="card-body grettings-box-two position-relative z-1 p-0">
                <div class="row align-items-center h-100">
                    <div class="col-lg-6">
                        <div class="grettings-box-two__content">
                            <h2 class="fw-medium mb-0 flex-align gap-10">Hi, <?php echo htmlspecialchars($current_user['full_name']); ?> <img src="assets/images/icons/wave-hand.png" alt=""> </h2>
                            <h2 class="fw-medium mb-16">What do you want to learn today with your partner?</h2>
                            <p class="text-15 text-gray-400">Discover courses, track progress, and achieve your learning goods seamlessly.</p>
                            <a href="my-learning.php" class="btn btn-main rounded-pill mt-32">Explore Courses</a>
                        </div>
                    </div>
                    <div class="col-lg-6 d-md-block d-none mt-auto">
                        <img src="assets/images/thumbs/gretting-thumb.png" alt="">
                    </div>
                </div>
                <img src="assets/images/bg/star-shape.png" class="position-absolute start-0 top-0 w-100 h-100 z-n1 object-fit-contain" alt="">
            </div>
        </div>
    </div>
    <div class="col-xxl-3 mb-20">
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
    </div>
    <!-- Calendar End -->

    <?php if (!empty($approvedClasses)): ?>
        <div class="row mb-20">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom border-gray-100 flex-align gap-8">
                        <h5 class="mb-0">My Registered Courses / Classes</h5>
                        <span class="py-2 px-8 bg-success-50 text-success-600 rounded-pill text-13"><?php echo count($approvedClasses); ?> Registered</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-20">
                            <?php foreach ($approvedClasses as $class): ?>
                                <?php
                                    $image_url = !empty($class['image']) ? 'uploads/classes/' . $class['image'] : null;
                                    $reg_status = 'Registered';
                                    $reg_badge = 'bg-success';
                                ?>
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="card border border-gray-100 h-100">
                                        <div class="card-body p-8">
                                            <a href="course-detail.php?id=<?php echo (int)$class['id']; ?>" class="bg-purple-100 rounded-8 overflow-hidden text-center mb-8 h-164 flex-center p-8 position-relative">
                                                <?php if ($image_url && file_exists($image_url)): ?>
                                                    <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($class['class_name']); ?>" class="w-100 h-100 object-fit-cover rounded-8">
                                                <?php else: ?>
                                                    <div class="text-center">
                                                        <i class="ph ph-book-open text-6xl text-purple-600 mb-3"></i>
                                                        <h6 class="text-purple-600 mb-0"><?php echo htmlspecialchars($class['class_name']); ?></h6>
                                                    </div>
                                                <?php endif; ?>
                                            </a>
                                            <div class="p-8">
                                                <div class="flex-between gap-8 mb-16">
                                                    <span class="text-13 py-2 px-10 rounded-pill bg-purple-50 text-purple-600"><?php echo htmlspecialchars($class['class_code']); ?></span>
                                                    <span class="text-13 py-2 px-10 rounded-pill <?php echo $reg_badge; ?> text-white"><?php echo $reg_status; ?></span>
                                                </div>
                                                <h5 class="mb-0"><a href="course-detail.php?id=<?php echo (int)$class['id']; ?>" class="hover-text-main-600"><?php echo htmlspecialchars($class['class_name']); ?></a></h5>
                                                <?php if (!empty($class['created_at'])): ?>
                                                    <div class="flex-align gap-8 flex-wrap mt-16">
                                                        <div class="flex-align gap-4">
                                                            <i class="ph ph-calendar text-main-600"></i>
                                                            <span class="text-gray-600 text-13">Created: <?php echo date('M d, Y', strtotime($class['created_at'])); ?></span>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($class['description'])): ?>
                                                    <?php
                                                    $desc = strlen($class['description']) > 80 ? substr($class['description'], 0, 80) . '...' : $class['description'];
                                                    ?>
                                                    <p class="text-gray-600 text-13 mt-12 mb-16"><?php echo htmlspecialchars($desc); ?></p>
                                                <?php endif; ?>
                                                <div class="flex-between gap-8 mt-16">
                                                    <div class="flex-align gap-8">
                                                        <!-- <a href="syllabi.php?class=<?php echo (int)$class['id']; ?>" class="btn btn-main rounded-pill py-6 px-12 text-13">
                                                            <i class="ph ph-book me-1"></i> Syllabi
                                                        </a> -->
                                                        <a href="lectures.php?class=<?php echo (int)$class['id']; ?>" class="btn btn-outline-main rounded-pill py-6 px-12 text-13">
                                                            <i class="ph ph-play me-1"></i> Lectures
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <!-- Close stats row and show available classes instead -->
</div>

<?php
// Build available lists (courses and classes) with images, open for registration
require_once 'classes/ClassModel.php';
$classModel = new ClassModel($db);
$accessible_ids = array_map(function ($c) {
    return (int)$c['id'];
}, $approvedClasses);

$available_courses_stmt = $classModel->read([], 'course', false);
$available_courses = [];
while ($row = $available_courses_stmt->fetch(PDO::FETCH_ASSOC)) {
    if (($row['registration_open'] ?? 0) == 1 && !in_array((int)$row['id'], $accessible_ids, true)) {
        $available_courses[] = $row;
    }
}

$available_classes_open_stmt = $classModel->read([], 'class', false);
$available_classes_open = [];
while ($row = $available_classes_open_stmt->fetch(PDO::FETCH_ASSOC)) {
    if (($row['registration_open'] ?? 0) == 1 && !in_array((int)$row['id'], $accessible_ids, true)) {
        $available_classes_open[] = $row;
    }
}
?>

<!-- Available To Register (Courses and Classes) -->
<div class="row mb-20">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom border-gray-100 flex-align gap-8">
                <h5 class="mb-0">Available To Register</h5>
                <span class="py-2 px-8 bg-main-50 text-main-600 rounded-pill text-13"><?php echo count($available_courses) + count($available_classes_open); ?> Available</span>
            </div>
            <div class="card-body">
                <?php if (empty($available_courses) && empty($available_classes_open)): ?>
                    <p class="text-gray-500 mb-0">No open items at the moment.</p>
                <?php else: ?>
                    <?php if (!empty($available_courses)): ?>
                        <h6 class="mb-12">Courses</h6>
                        <div class="row g-20 mb-20">
                            <?php foreach ($available_courses as $ac): ?>
                                <?php
                                $image_url = !empty($ac['image']) ? 'uploads/classes/' . $ac['image'] : null;
                                $reg_status = 'Open';
                                $reg_badge = 'bg-success';
                                ?>
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="card border border-gray-100 h-100">
                                        <div class="card-body p-8">
                                            <a href="course-detail.php?id=<?php echo (int)$ac['id']; ?>" class="bg-purple-100 rounded-8 overflow-hidden text-center mb-8 h-164 flex-center p-8 position-relative">
                                                <?php if ($image_url && file_exists($image_url)): ?>
                                                    <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($ac['class_name']); ?>" class="w-100 h-100 object-fit-cover rounded-8">
                                                <?php else: ?>
                                                    <div class="text-center">
                                                        <i class="ph ph-book-open text-6xl text-purple-600 mb-3"></i>
                                                        <h6 class="text-purple-600 mb-0"><?php echo htmlspecialchars($ac['class_name']); ?></h6>
                                                    </div>
                                                <?php endif; ?>
                                            </a>
                                            <div class="p-8">
                                                <div class="flex-between gap-8 mb-16">
                                                    <span class="text-13 py-2 px-10 rounded-pill bg-purple-50 text-purple-600"><?php echo htmlspecialchars($ac['class_code']); ?></span>
                                                    <span class="text-13 py-2 px-10 rounded-pill <?php echo $reg_badge; ?> text-white"><?php echo $reg_status; ?></span>
                                                </div>
                                                <h5 class="mb-0"><a href="course-detail.php?id=<?php echo (int)$ac['id']; ?>" class="hover-text-main-600"><?php echo htmlspecialchars($ac['class_name']); ?></a></h5>
                                                <?php if (!empty($ac['created_at'])): ?>
                                                    <div class="flex-align gap-8 flex-wrap mt-16">
                                                        <div class="flex-align gap-4">
                                                            <i class="ph ph-calendar text-main-600"></i>
                                                            <span class="text-gray-600 text-13">Created: <?php echo date('M d, Y', strtotime($ac['created_at'])); ?></span>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($ac['description'])): ?>
                                                    <?php
                                                    $desc = strlen($ac['description']) > 80 ? substr($ac['description'], 0, 80) . '...' : $ac['description'];
                                                    ?>
                                                    <p class="text-gray-600 text-13 mt-12 mb-16"><?php echo htmlspecialchars($desc); ?></p>
                                                <?php endif; ?>
                                                <div class="flex-between gap-8 mt-16">
                                                    <div class="flex-align gap-8">
                                                        <a href="course-detail.php?id=<?php echo (int)$ac['id']; ?>" class="btn btn-main rounded-pill py-6 px-12 text-13">
                                                            <i class="ph ph-user-plus me-1"></i> Register
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($available_classes_open)): ?>
                        <h6 class="mb-12">Classes</h6>
                        <div class="row g-20">
                            <?php foreach ($available_classes_open as $ac): ?>
                                <?php
                                $image_url = !empty($ac['image']) ? 'uploads/classes/' . $ac['image'] : null;
                                $reg_status = 'Open';
                                $reg_badge = 'bg-success';
                                ?>
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="card border border-gray-100 h-100">
                                        <div class="card-body p-8">
                                            <a href="course-detail.php?id=<?php echo (int)$ac['id']; ?>" class="bg-purple-100 rounded-8 overflow-hidden text-center mb-8 h-164 flex-center p-8 position-relative">
                                                <?php if ($image_url && file_exists($image_url)): ?>
                                                    <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($ac['class_name']); ?>" class="w-100 h-100 object-fit-cover rounded-8">
                                                <?php else: ?>
                                                    <div class="text-center">
                                                        <i class="ph ph-graduation-cap text-6xl text-purple-600 mb-3"></i>
                                                        <h6 class="text-purple-600 mb-0"><?php echo htmlspecialchars($ac['class_name']); ?></h6>
                                                    </div>
                                                <?php endif; ?>
                                            </a>
                                            <div class="p-8">
                                                <div class="flex-between gap-8 mb-16">
                                                    <span class="text-13 py-2 px-10 rounded-pill bg-purple-50 text-purple-600"><?php echo htmlspecialchars($ac['class_code']); ?></span>
                                                    <span class="text-13 py-2 px-10 rounded-pill <?php echo $reg_badge; ?> text-white"><?php echo $reg_status; ?></span>
                                                </div>
                                                <h5 class="mb-0"><a href="course-detail.php?id=<?php echo (int)$ac['id']; ?>" class="hover-text-main-600"><?php echo htmlspecialchars($ac['class_name']); ?></a></h5>
                                                <?php if (!empty($ac['created_at'])): ?>
                                                    <div class="flex-align gap-8 flex-wrap mt-16">
                                                        <div class="flex-align gap-4">
                                                            <i class="ph ph-calendar text-main-600"></i>
                                                            <span class="text-gray-600 text-13">Created: <?php echo date('M d, Y', strtotime($ac['created_at'])); ?></span>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($ac['description'])): ?>
                                                    <?php
                                                    $desc = strlen($ac['description']) > 80 ? substr($ac['description'], 0, 80) . '...' : $ac['description'];
                                                    ?>
                                                    <p class="text-gray-600 text-13 mt-12 mb-16"><?php echo htmlspecialchars($desc); ?></p>
                                                <?php endif; ?>
                                                <div class="flex-between gap-8 mt-16">
                                                    <div class="flex-align gap-8">
                                                        <a href="course-detail.php?id=<?php echo (int)$ac['id']; ?>" class="btn btn-main rounded-pill py-6 px-12 text-13">
                                                            <i class="ph ph-user-plus me-1"></i> Register
                                                        </a>
                                                        <a href="subjects.php?class=<?php echo (int)$ac['id']; ?>" class="btn btn-outline-main rounded-pill py-6 px-12 text-13">
                                                            <i class="ph ph-list me-1"></i> Subjects
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
