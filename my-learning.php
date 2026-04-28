<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'classes/User.php';
require_once 'classes/ClassModel.php';
require_once 'classes/BatchEnrollment.php';
require_once 'classes/ClassInquiry.php';

require_roles(['solo_student','student']);

$current_user = current_user();

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$classModel = new ClassModel($db);
$enrollmentModel = new BatchEnrollment($db);
$classInquiry = new ClassInquiry($db);

// Enrollments (courses via batches)
$enrollments = $enrollmentModel->getEnrollmentsByUser($current_user['id']);
$enrolled_class_ids = [];
foreach ($enrollments as $en) {
    if (!empty($en['class_name'])) {
        $enrolled_class_ids[] = (int)$en['class_id'];
    }
}

// Accessible classes (direct access)
$accessible_classes = $user->getAccessibleClasses($current_user);
$accessible_class_ids = array_map(function($c){ return (int)$c['id']; }, $accessible_classes);

// User inquiries for stats
$userInquiriesStmt = $classInquiry->getUserInquiries($current_user['id']);
$userInquiries = $userInquiriesStmt ? $userInquiriesStmt->fetchAll(PDO::FETCH_ASSOC) : [];

// Combine for a set of registered items
$registered_ids = array_unique(array_merge($enrolled_class_ids, $accessible_class_ids));

// Available to register: active courses open for registration not in registered_ids
$available_courses_stmt = $classModel->read([], 'course', false);
$available_courses = [];
while ($row = $available_courses_stmt->fetch(PDO::FETCH_ASSOC)) {
    if (($row['registration_open'] ?? 0) == 1 && !in_array((int)$row['id'], $registered_ids, true)) {
        $available_courses[] = $row;
    }
}

// Available classes open for registration (not accessible)
$available_classes_stmt = $classModel->read([], 'class', false);
$available_classes = [];
while ($row = $available_classes_stmt->fetch(PDO::FETCH_ASSOC)) {
    if (($row['registration_open'] ?? 0) == 1 && !in_array((int)$row['id'], $registered_ids, true)) {
        $available_classes[] = $row;
    }
}

$flash = get_flash_message();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Learning - <?php echo APP_NAME; ?></title>
    <link rel="shortcut icon" href="assets/images/logo/favicon.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
</head>
<body>

<div class="preloader"><div class="loader"></div></div>
<div class="side-overlay"></div>

<?php include 'includes/sidebar_new.php'; ?>

<div class="dashboard-main-wrapper">
    <?php include 'includes/navbar_new.php'; ?>

    <div class="dashboard-body">
        <!-- Learning Stats (moved from solo student dashboard) -->
        <div class="row mb-20">
            <div class="col-xxl-3 col-sm-6 mb-20">
                <div class="card">
                    <div class="card-body">
                        <div class="flex-between gap-8 mb-16">
                            <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-main-100 text-main-600 text-2xl"><i class="ph-fill ph-graduation-cap"></i></span>
                        </div>
                        <div>
                            <h4 class="mb-2"><?php echo count($accessible_classes); ?></h4>
                            <span class="text-gray-300">Enrolled Classes</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6 mb-20">
                <div class="card">
                    <div class="card-body">
                        <div class="flex-between gap-8 mb-16">
                            <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-warning-100 text-warning-600 text-2xl"><i class="ph-fill ph-clock"></i></span>
                        </div>
                        <div>
                            <h4 class="mb-2"><?php echo count(array_filter($userInquiries, function ($i) { return ($i['status'] ?? '') === 'pending'; })); ?></h4>
                            <span class="text-gray-300">Pending Inquiries</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6 mb-20">
                <div class="card">
                    <div class="card-body">
                        <div class="flex-between gap-8 mb-16">
                            <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-success-100 text-success-600 text-2xl"><i class="ph-fill ph-check-circle"></i></span>
                        </div>
                        <div>
                            <h4 class="mb-2"><?php echo count(array_filter($userInquiries, function ($i) { return ($i['status'] ?? '') === 'approved'; })); ?></h4>
                            <span class="text-gray-300">Approved Inquiries</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6 mb-20">
                <div class="card">
                    <div class="card-body">
                        <div class="flex-between gap-8 mb-16">
                            <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-danger-100 text-danger-600 text-2xl"><i class="ph-fill ph-x-circle"></i></span>
                        </div>
                        <div>
                            <h4 class="mb-2"><?php echo count(array_filter($userInquiries, function ($i) { return ($i['status'] ?? '') === 'rejected'; })); ?></h4>
                            <span class="text-gray-300">Rejected Inquiries</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gy-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">My Registered Courses / Classes</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($flash): ?>
                            <div class="alert alert-<?php echo $flash['type']==='success'?'success':'info'; ?> alert-dismissible fade show" role="alert">
                                <?php echo $flash['message']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($registered_ids)): ?>
                            <?php
                            // Build a unified registered list keyed by class ID
                            $registered_map = [];
                            foreach ($enrollments as $en) {
                                $cid = (int)($en['class_id'] ?? 0);
                                if ($cid === 0) continue;
                                $registered_map[$cid] = [
                                    'id' => $cid,
                                    'class_name' => $en['class_name'] ?? '',
                                    'class_code' => $en['class_code'] ?? '',
                                    'image' => $en['class_image'] ?? null,
                                    'description' => $en['class_description'] ?? null,
                                    'created_at' => $en['class_created_at'] ?? null,
                                ];
                            }
                            foreach ($accessible_classes as $cls) {
                                $cid = (int)$cls['id'];
                                if (!isset($registered_map[$cid])) {
                                    $registered_map[$cid] = $cls;
                                }
                            }
                            ?>
                            <div class="row g-20">
                                <?php foreach ($registered_map as $rc): ?>
                                    <?php 
                                        $image_url = !empty($rc['image']) ? 'uploads/classes/' . $rc['image'] : null;
                                        $reg_status = 'Registered';
                                        $reg_badge = 'bg-success';
                                    ?>
                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="card border border-gray-100 h-100">
                                            <div class="card-body p-8">
                                                <a href="course-detail.php?id=<?php echo (int)$rc['id']; ?>" class="bg-purple-100 rounded-8 overflow-hidden text-center mb-8 h-164 flex-center p-8 position-relative">
                                                    <?php if ($image_url && file_exists($image_url)): ?>
                                                        <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($rc['class_name']); ?>" class="w-100 h-100 object-fit-cover rounded-8">
                                                    <?php else: ?>
                                                        <div class="text-center">
                                                            <i class="ph ph-book-open text-6xl text-purple-600 mb-3"></i>
                                                            <h6 class="text-purple-600 mb-0"><?php echo htmlspecialchars($rc['class_name']); ?></h6>
                                                        </div>
                                                    <?php endif; ?>
                                                </a>
                                                <div class="p-8">
                                                    <div class="flex-between gap-8 mb-16">
                                                        <span class="text-13 py-2 px-10 rounded-pill bg-purple-50 text-purple-600"><?php echo htmlspecialchars($rc['class_code']); ?></span>
                                                        <span class="text-13 py-2 px-10 rounded-pill <?php echo $reg_badge; ?> text-white"><?php echo $reg_status; ?></span>
                                                    </div>
                                                    <h5 class="mb-0"><a href="course-detail.php?id=<?php echo (int)$rc['id']; ?>" class="hover-text-main-600"><?php echo htmlspecialchars($rc['class_name']); ?></a></h5>
                                                    <?php if (!empty($rc['created_at'])): ?>
                                                        <div class="flex-align gap-8 flex-wrap mt-16">
                                                            <div class="flex-align gap-4">
                                                                <i class="ph ph-calendar text-main-600"></i>
                                                                <span class="text-gray-600 text-13">Created: <?php echo date('M d, Y', strtotime($rc['created_at'])); ?></span>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if (!empty($rc['description'])): ?>
                                                        <?php $desc = strlen($rc['description']) > 80 ? substr($rc['description'], 0, 80) . '...' : $rc['description']; ?>
                                                        <p class="text-gray-600 text-13 mt-12 mb-16"><?php echo htmlspecialchars($desc); ?></p>
                                                    <?php endif; ?>
                                                    <div class="flex-between gap-8 mt-16">
                                                        <div class="flex-align gap-8">
                                                            <!-- <a href="syllabi.php?class=<?php echo (int)$rc['id']; ?>" class="btn btn-main rounded-pill py-6 px-12 text-13">
                                                                <i class="ph ph-book me-1"></i> Syllabi
                                                            </a> -->
                                                            <a href="lectures.php?class=<?php echo (int)$rc['id']; ?>" class="btn btn-outline-main rounded-pill py-6 px-12 text-13">
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
                        <?php else: ?>
                            <p class="text-gray-600 mb-0">You have no registrations yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom border-gray-100 flex-align gap-8">
                        <h5 class="card-title mb-0">Available To Register</h5>
                        <span class="py-2 px-8 bg-main-50 text-main-600 rounded-pill text-13"><?php echo count($available_courses) + count($available_classes); ?> Available</span>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-12">Courses</h6>
                        <div class="row g-20 mb-24">
                            <?php if (!empty($available_courses)): foreach ($available_courses as $c): ?>
                                <?php 
                                    $image_url = !empty($c['image']) ? 'uploads/classes/' . $c['image'] : null;
                                    $reg_status = 'Open';
                                    $reg_badge = 'bg-success';
                                ?>
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="card border border-gray-100 h-100">
                                        <div class="card-body p-8">
                                            <a href="course-detail.php?id=<?php echo (int)$c['id']; ?>" class="bg-purple-100 rounded-8 overflow-hidden text-center mb-8 h-164 flex-center p-8 position-relative">
                                                <?php if ($image_url && file_exists($image_url)): ?>
                                                    <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($c['class_name']); ?>" class="w-100 h-100 object-fit-cover rounded-8">
                                                <?php else: ?>
                                                    <div class="text-center">
                                                        <i class="ph ph-book-open text-6xl text-purple-600 mb-3"></i>
                                                        <h6 class="text-purple-600 mb-0"><?php echo htmlspecialchars($c['class_name']); ?></h6>
                                                    </div>
                                                <?php endif; ?>
                                            </a>
                                            <div class="p-8">
                                                <div class="flex-between gap-8 mb-16">
                                                    <span class="text-13 py-2 px-10 rounded-pill bg-purple-50 text-purple-600"><?php echo htmlspecialchars($c['class_code']); ?></span>
                                                    <span class="text-13 py-2 px-10 rounded-pill <?php echo $reg_badge; ?> text-white"><?php echo $reg_status; ?></span>
                                                </div>
                                                <h5 class="mb-0"><a href="course-detail.php?id=<?php echo (int)$c['id']; ?>" class="hover-text-main-600"><?php echo htmlspecialchars($c['class_name']); ?></a></h5>
                                                <?php if (!empty($c['created_at'])): ?>
                                                    <div class="flex-align gap-8 flex-wrap mt-16">
                                                        <div class="flex-align gap-4">
                                                            <i class="ph ph-calendar text-main-600"></i>
                                                            <span class="text-gray-600 text-13">Created: <?php echo date('M d, Y', strtotime($c['created_at'])); ?></span>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($c['description'])): ?>
                                                    <?php 
                                                        $desc = strlen($c['description']) > 80 ? substr($c['description'], 0, 80) . '...' : $c['description'];
                                                    ?>
                                                    <p class="text-gray-600 text-13 mt-12 mb-16"><?php echo htmlspecialchars($desc); ?></p>
                                                <?php endif; ?>
                                                <div class="flex-between gap-8 mt-16">
                                                    <div class="flex-align gap-8">
                                                        <a href="course-detail.php?id=<?php echo (int)$c['id']; ?>" class="btn btn-main rounded-pill py-6 px-12 text-13">
                                                            <i class="ph ph-user-plus me-1"></i> Register
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; else: ?>
                                <p class="text-gray-500 mb-0">No courses available at the moment.</p>
                            <?php endif; ?>
                        </div>

                        <h6 class="mb-12">Classes</h6>
                        <div class="row g-20">
                            <?php if (!empty($available_classes)): foreach ($available_classes as $c): ?>
                                <?php 
                                    $image_url = !empty($c['image']) ? 'uploads/classes/' . $c['image'] : null;
                                    $reg_status = 'Open';
                                    $reg_badge = 'bg-success';
                                ?>
                                <div class="col-xxl-3 col-lg-4 col-sm-6">
                                    <div class="card border border-gray-100 h-100">
                                        <div class="card-body p-8">
                                            <a href="course-detail.php?id=<?php echo (int)$c['id']; ?>" class="bg-purple-100 rounded-8 overflow-hidden text-center mb-8 h-164 flex-center p-8 position-relative">
                                                <?php if ($image_url && file_exists($image_url)): ?>
                                                    <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($c['class_name']); ?>" class="w-100 h-100 object-fit-cover rounded-8">
                                                <?php else: ?>
                                                    <div class="text-center">
                                                        <i class="ph ph-graduation-cap text-6xl text-purple-600 mb-3"></i>
                                                        <h6 class="text-purple-600 mb-0"><?php echo htmlspecialchars($c['class_name']); ?></h6>
                                                    </div>
                                                <?php endif; ?>
                                            </a>
                                            <div class="p-8">
                                                <div class="flex-between gap-8 mb-16">
                                                    <span class="text-13 py-2 px-10 rounded-pill bg-purple-50 text-purple-600"><?php echo htmlspecialchars($c['class_code']); ?></span>
                                                    <span class="text-13 py-2 px-10 rounded-pill <?php echo $reg_badge; ?> text-white"><?php echo $reg_status; ?></span>
                                                </div>
                                                <h5 class="mb-0"><a href="course-detail.php?id=<?php echo (int)$c['id']; ?>" class="hover-text-main-600"><?php echo htmlspecialchars($c['class_name']); ?></a></h5>
                                                <?php if (!empty($c['created_at'])): ?>
                                                    <div class="flex-align gap-8 flex-wrap mt-16">
                                                        <div class="flex-align gap-4">
                                                            <i class="ph ph-calendar text-main-600"></i>
                                                            <span class="text-gray-600 text-13">Created: <?php echo date('M d, Y', strtotime($c['created_at'])); ?></span>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($c['description'])): ?>
                                                    <?php 
                                                        $desc = strlen($c['description']) > 80 ? substr($c['description'], 0, 80) . '...' : $c['description'];
                                                    ?>
                                                    <p class="text-gray-600 text-13 mt-12 mb-16"><?php echo htmlspecialchars($desc); ?></p>
                                                <?php endif; ?>
                                                <div class="flex-between gap-8 mt-16">
                                                    <div class="flex-align gap-8">
                                                        <a href="course-detail.php?id=<?php echo (int)$c['id']; ?>" class="btn btn-main rounded-pill py-6 px-12 text-13">
                                                            <i class="ph ph-user-plus me-1"></i> Register
                                                        </a>
                                                        <!-- <a href="subjects.php?class=<?php echo (int)$c['id']; ?>" class="btn btn-outline-main rounded-pill py-6 px-12 text-13">
                                                            <i class="ph ph-list me-1"></i> Subjects
                                                        </a> -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; else: ?>
                                <p class="text-gray-500 mb-0">No classes available at the moment.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/js/boostrap.bundle.min.js"></script>
<script src="assets/js/phosphor-icon.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
