<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'classes/Lecture.php';
require_once 'classes/Syllabus.php';

// No login required - this is a public page

$database = new Database();
$db = $database->getConnection();
$lecture = new Lecture($db);
$syllabusModel = new Syllabus($db);

// Get syllabus ID from URL
$syllabus_id = isset($_GET['syllabus']) ? (int)$_GET['syllabus'] : 0;

if ($syllabus_id <= 0) {
    die('Invalid syllabus ID');
}

// Get syllabus information
$syllabusModel->id = $syllabus_id;
if (!$syllabusModel->readOne([])) {
    die('Syllabus not found');
}

// Get lectures for this syllabus (no access restrictions for public page)
$lectures_stmt = $lecture->readBySyllabus($syllabus_id, []);

// Get action for viewing individual lecture
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$lecture_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// If viewing a single lecture
if ($action == 'view' && $lecture_id > 0) {
    $lecture->id = $lecture_id;
    if (!$lecture->readOne([])) {
        die('Lecture not found');
    }
    
    // Check if this lecture belongs to the syllabus
    if ($lecture->syllabus_id != $syllabus_id) {
        die('Lecture does not belong to this syllabus');
    }
    
    // Check if this is a multiple format lecture
    $is_multiple_format = ($lecture->lecture_type === 'multiple');
    $lecture_files = [];
    if ($is_multiple_format) {
        $lecture_files = $lecture->getLectureFiles($lecture_id);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($syllabusModel->syllabus_title); ?> - <?php echo APP_NAME; ?></title>
    <link rel="shortcut icon" href="assets/images/logo/favicon.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/plyr.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        .public-header {
            background: linear-gradient(135deg, hsl(var(--main)) 0%, var(--main-800) 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
        }
        .public-header-media {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: nowrap;
        }
        .public-header-media img {
            width: 80px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.15);
        }
        .public-header-media .public-header-text {
            min-width: 0;
            flex: 1;
        }
        .lecture-card-image {
            width: 100%;
            height: 164px;
            object-fit: cover;
            border-radius: 8px;
        }
        .text-line-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body>
    
<div class="preloader">
    <div class="loader"></div>
</div>

<div class="public-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="public-header-media">
                    <img src="assets/images/class-images/images.jpeg" alt="Syllabus cover">
                    <div class="public-header-text">
                        <h1 class="mb-3"><?php echo htmlspecialchars($syllabusModel->syllabus_title); ?></h1>
                        <?php if (!empty($syllabusModel->description)): ?>
                            <p class="mb-0 opacity-90"><?php echo htmlspecialchars($syllabusModel->description); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <?php if ($syllabusModel->duration_weeks): ?>
                    <span class="badge bg-white text-dark px-3 py-2 mb-2 d-inline-block">
                        <i class="ph ph-calendar me-1"></i> <?php echo $syllabusModel->duration_weeks; ?> weeks
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <?php if ($action == 'list'): ?>
        <!-- Lectures List -->
        <div class="row g-20 mb-4">
            <?php
            if ($lectures_stmt->rowCount() > 0) {
                while ($row = $lectures_stmt->fetch()) {
                    $type_class = 'type-' . $row['lecture_type'];
                    $type_color = '';
                    $type_icon = '';
                    switch ($row['lecture_type']) {
                        case 'video': 
                            $type_color = 'success'; 
                            $type_icon = 'ph-video-camera'; 
                            break;
                        case 'audio': 
                            $type_color = 'warning'; 
                            $type_icon = 'ph-speaker-high'; 
                            break;
                        case 'file': 
                            $type_color = 'info'; 
                            $type_icon = 'ph-file'; 
                            break;
                        case 'text': 
                            $type_color = 'danger'; 
                            $type_icon = 'ph-article'; 
                            break;
                        case 'pptx_embed': 
                            $type_color = 'purple'; 
                            $type_icon = 'ph-presentation'; 
                            break;
                        case 'multiple': 
                            $type_color = 'primary'; 
                            $type_icon = 'ph-stack'; 
                            break;
                    }
            ?>
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="card border border-gray-100">
                    <div class="card-body p-8">
                        <a href="public-syllabus.php?syllabus=<?php echo $syllabus_id; ?>&action=view&id=<?php echo $row['id']; ?>" class="bg-main-100 rounded-8 overflow-hidden text-center mb-8 h-164 flex-center p-8">
                            <?php if (!empty($row['image']) && file_exists($row['image'])): ?>
                                <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['lecture_title']); ?>" class="lecture-card-image">
                            <?php else: ?>
                                <span class="text-6xl text-main-600"><i class="<?php echo $type_icon; ?>"></i></span>
                            <?php endif; ?>
                        </a>
                        <div class="p-8">
                            <span class="text-13 py-2 px-10 rounded-pill bg-<?php echo $type_color; ?>-50 text-<?php echo $type_color; ?>-600 mb-16">
                                <?php echo $row['lecture_type'] == 'multiple' ? 'Multiple Formats' : ucfirst($row['lecture_type']); ?>
                            </span>
                            <h5 class="mb-0"><a href="public-syllabus.php?syllabus=<?php echo $syllabus_id; ?>&action=view&id=<?php echo $row['id']; ?>" class="hover-text-main-600"><?php echo htmlspecialchars($row['lecture_title']); ?></a></h5>

                            <?php if (!empty($row['description'])): ?>
                            <p class="text-gray-300 text-13 mt-8 text-line-2"><?php echo htmlspecialchars(substr($row['description'], 0, 100)); ?><?php echo strlen($row['description']) > 100 ? '...' : ''; ?></p>
                            <?php endif; ?>

                            <div class="flex-between gap-8 mt-12 pt-12 border-top border-gray-100">
                                <div class="flex-align gap-4">
                                    <span class="text-sm text-main-600 d-flex"><i class="ph ph-list-numbers"></i></span>
                                    <span class="text-13 text-gray-600">Order: <?php echo $row['lecture_order']; ?></span>
                                </div>
                                <?php if ($row['duration_minutes']): ?>
                                <div class="flex-align gap-4">
                                    <span class="text-sm text-main-600 d-flex"><i class="ph ph-clock"></i></span>
                                    <span class="text-13 text-gray-600"><?php echo $row['duration_minutes']; ?> min</span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mt-16">
                                <a href="public-syllabus.php?syllabus=<?php echo $syllabus_id; ?>&action=view&id=<?php echo $row['id']; ?>" class="btn btn-main rounded-pill py-9 w-100">View Lecture</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                }
            } else {
                echo '<div class="col-12 text-center py-5">';
                echo '<div class="bg-gray-50 rounded-12 p-40">';
                echo '<i class="ph ph-books text-6xl text-gray-400 mb-16 d-block"></i>';
                echo '<h5 class="text-gray-500 mb-8">No Lectures Found</h5>';
                echo '<p class="text-gray-400">There are no lectures available for this syllabus yet.</p>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>

    <?php elseif ($action == 'view' && $lecture_id > 0): ?>
        <!-- Single Lecture View -->
        <div class="row gy-4">
            <div class="col-12 mb-3">
                <a href="public-syllabus.php?syllabus=<?php echo $syllabus_id; ?>" class="btn btn-outline-main rounded-pill">
                    <i class="ph ph-arrow-left me-2"></i> Back to Lectures
                </a>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body p-lg-20">
                        <div class="flex-between flex-wrap gap-12 mb-20">
                            <div>
                                <h3 class="mb-4"><?php echo htmlspecialchars($lecture->lecture_title); ?></h3>
                                <p class="text-gray-600 text-15">Order: <?php echo htmlspecialchars($lecture->lecture_order); ?></p>
                            </div>

                            <div class="flex-align flex-wrap gap-24">
                                <span class="py-6 px-16 bg-main-50 text-main-600 rounded-pill text-15">
                                    <?php echo $is_multiple_format ? 'Multiple Formats' : ucfirst($lecture->lecture_type); ?>
                                </span>
                                <?php if ($lecture->duration_minutes): ?>
                                    <span class="py-6 px-16 bg-success-50 text-success-600 rounded-pill text-15"><?php echo $lecture->duration_minutes; ?> min</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($lecture->image) && file_exists($lecture->image)): ?>
                        <div class="mb-24">
                            <img src="<?php echo htmlspecialchars($lecture->image); ?>" alt="<?php echo htmlspecialchars($lecture->lecture_title); ?>" class="w-100 rounded-8" style="max-height: 400px; object-fit: cover;">
                        </div>
                        <?php endif; ?>

                        <?php if ($is_multiple_format && !empty($lecture_files)): ?>
                            <!-- Multiple Format Selector -->
                            <div class="mb-24 pb-24 border-bottom border-gray-100">
                                <h5 class="mb-12 fw-bold">Choose Format</h5>
                                <div class="flex-align gap-8 flex-wrap">
                                    <?php 
                                    $first = true;
                                    foreach ($lecture_files as $file): 
                                        $icon_class = '';
                                        $label = '';
                                        switch($file['file_type']) {
                                            case 'pdf':
                                                $icon_class = 'ph-file-pdf';
                                                $label = 'PDF';
                                                break;
                                            case 'pptx':
                                                $icon_class = 'ph-presentation';
                                                $label = 'PowerPoint';
                                                break;
                                            case 'video':
                                                $icon_class = 'ph-video-camera';
                                                $label = 'Video';
                                                break;
                                            case 'audio':
                                                $icon_class = 'ph-speaker-high';
                                                $label = 'Audio';
                                                break;
                                            case 'text':
                                                $icon_class = 'ph-article';
                                                $label = 'Text';
                                                break;
                                        }
                                    ?>
                                        <button type="button" 
                                                class="btn <?php echo $first ? 'btn-main' : 'btn-outline-main'; ?> rounded-pill py-7 px-16 format-btn" 
                                                onclick="switchFormat(<?php echo $lecture_id; ?>, '<?php echo $file['file_type']; ?>')">
                                            <i class="<?php echo $icon_class; ?> me-1"></i> <?php echo $label; ?>
                                        </button>
                                        <?php 
                                            $first = false;
                                        endforeach; 
                                        ?>
                                </div>
                            </div>

                            <?php if (!empty($lecture->description)): ?>
                            <div class="mb-24 pb-24 border-bottom border-gray-100">
                                <h5 class="mb-12 fw-bold">Description</h5>
                                <p class="text-gray-300 text-15"><?php echo htmlspecialchars($lecture->description); ?></p>
                            </div>
                            <?php endif; ?>

                            <!-- Format Content Display -->
                            <div class="lecture-content-display">
                                <?php 
                                $first_display = true;
                                foreach ($lecture_files as $file): 
                                ?>
                                    <div id="format-<?php echo $file['file_type']; ?>" class="format-content" style="display: <?php echo $first_display ? 'block' : 'none'; ?>;">
                                        <?php if ($file['file_type'] == 'pdf'): ?>
                                            <div class="mb-20">
                                                <iframe src="<?php echo htmlspecialchars($file['file_url']); ?>" 
                                                        width="100%" 
                                                        height="800px" 
                                                        style="border: none; border-radius: 8px;">
                                                </iframe>
                                            </div>

                                        <?php elseif ($file['file_type'] == 'pptx'): ?>
                                            <div class="mb-20">
                                                <?php
                                                $pptx_content = $file['file_url'];
                                                if (strpos($pptx_content, '<iframe') !== false) {
                                                    echo $pptx_content;
                                                } else {
                                                    ?>
                                                    <iframe src="<?php echo htmlspecialchars($pptx_content); ?>" 
                                                            width="100%" 
                                                            height="600px" 
                                                            style="border: 1px solid #ccc; border-radius: 8px;"
                                                            allowfullscreen>
                                                    </iframe>
                                                    <?php
                                                }
                                                ?>
                                                <div class="mt-3 text-center">
                                                    <a href="<?php echo htmlspecialchars(strip_tags($pptx_content)); ?>" 
                                                       class="btn btn-outline-main rounded-pill" 
                                                       target="_blank">
                                                        <i class="ph ph-arrow-square-out me-2"></i>Open in New Tab
                                                    </a>
                                                </div>
                                            </div>

                                        <?php elseif ($file['file_type'] == 'video'): ?>
                                            <?php 
                                                $vurl = trim($file['file_url'] ?? '');
                                                $iframe_src = '';
                                                if (stripos($vurl, '<iframe') !== false) {
                                                    echo '<div class="ratio ratio-16x9 rounded-16 overflow-hidden mb-20">' . $vurl . '</div>';
                                                } else {
                                                    if (preg_match('/youtu\.be\/([\w-]+)/i', $vurl, $m) || preg_match('/youtube\.com\/(?:watch\?v=|embed\/)([\w-]+)/i', $vurl, $m)) {
                                                        $iframe_src = 'https://www.youtube.com/embed/' . $m[1];
                                                    } elseif (preg_match('/vimeo\.com\/(\d+)/i', $vurl, $m)) {
                                                        $iframe_src = 'https://player.vimeo.com/video/' . $m[1];
                                                    } elseif (preg_match('/facebook\.com\/.*\/videos\/(\d+)/i', $vurl)) {
                                                        $iframe_src = 'https://www.facebook.com/plugins/video.php?href=' . urlencode($vurl) . '&show_text=0&width=1280';
                                                    } elseif (preg_match('/drive\.google\.com\/file\/d\/([^\/]+)/i', $vurl, $m)) {
                                                        $iframe_src = 'https://drive.google.com/file/d/' . $m[1] . '/preview';
                                                    } elseif (preg_match('/(?:open|uc)\?id=([^&]+)/i', $vurl, $m)) {
                                                        $iframe_src = 'https://drive.google.com/file/d/' . $m[1] . '/preview';
                                                    }

                                                    if ($iframe_src) {
                                                        echo '<div class="ratio ratio-16x9 rounded-16 overflow-hidden mb-20"><iframe src="' . htmlspecialchars($iframe_src) . '" allowfullscreen frameborder="0"></iframe></div>';
                                                    } else {
                                                        echo '<div class="rounded-16 overflow-hidden mb-20"><video class="player w-100" controls><source src="' . htmlspecialchars($vurl) . '">Your browser does not support the video tag.</video></div>';
                                                    }
                                                }
                                            ?>
                                            <?php if ($file['duration_minutes']): ?>
                                                <p class="text-gray-600 text-center">
                                                    <i class="ph ph-clock me-1"></i>Duration: <?php echo $file['duration_minutes']; ?> minutes
                                                </p>
                                            <?php endif; ?>

                                        <?php elseif ($file['file_type'] == 'audio'): ?>
                                            <div class="mb-20">
                                                <audio controls class="w-100">
                                                    <source src="<?php echo htmlspecialchars($file['file_url']); ?>">
                                                    Your browser does not support the audio element.
                                                </audio>
                                            </div>
                                            <?php if ($file['duration_minutes']): ?>
                                                <p class="text-gray-600 text-center">
                                                    <i class="ph ph-clock me-1"></i>Duration: <?php echo $file['duration_minutes']; ?> minutes
                                                </p>
                                            <?php endif; ?>

                                        <?php elseif ($file['file_type'] == 'text'): ?>
                                            <div class="content-text-block p-4 bg-light rounded-8">
                                                <?php echo $file['text_content']; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php 
                                    $first_display = false;
                                endforeach; 
                                ?>
                            </div>

                        <?php else: ?>
                            <!-- Single Format Display -->
                            <?php if (!empty($lecture->description)): ?>
                            <div class="mb-24 pb-24 border-bottom border-gray-100">
                                <h5 class="mb-12 fw-bold">Description</h5>
                                <p class="text-gray-300 text-15"><?php echo htmlspecialchars($lecture->description); ?></p>
                            </div>
                            <?php endif; ?>

                            <div class="lecture-content-display">
                                <?php if ($lecture->lecture_type == 'text'): ?>
                                    <div class="content-text-block">
                                        <?php echo $lecture->text_content; ?>
                                    </div>
                                <?php elseif ($lecture->lecture_type == 'video' && $lecture->content_url): ?>
                                    <?php 
                                        $vurl = trim($lecture->content_url);
                                        $iframe_src = '';
                                        if (stripos($vurl, '<iframe') !== false) {
                                            echo '<div class="ratio ratio-16x9 rounded-16 overflow-hidden mb-20">' . $vurl . '</div>';
                                        } else {
                                            if (preg_match('/youtu\.be\/([\w-]+)/i', $vurl, $m) || preg_match('/youtube\.com\/(?:watch\?v=|embed\/)([\w-]+)/i', $vurl, $m)) {
                                                $iframe_src = 'https://www.youtube.com/embed/' . $m[1];
                                            } elseif (preg_match('/vimeo\.com\/(\d+)/i', $vurl, $m)) {
                                                $iframe_src = 'https://player.vimeo.com/video/' . $m[1];
                                            } elseif (preg_match('/facebook\.com\/.*\/videos\/(\d+)/i', $vurl)) {
                                                $iframe_src = 'https://www.facebook.com/plugins/video.php?href=' . urlencode($vurl) . '&show_text=0&width=1280';
                                            } elseif (preg_match('/drive\.google\.com\/file\/d\/([^\/]+)/i', $vurl, $m)) {
                                                $iframe_src = 'https://drive.google.com/file/d/' . $m[1] . '/preview';
                                            } elseif (preg_match('/(?:open|uc)\?id=([^&]+)/i', $vurl, $m)) {
                                                $iframe_src = 'https://drive.google.com/file/d/' . $m[1] . '/preview';
                                            }
                                            if ($iframe_src) {
                                                echo '<div class="ratio ratio-16x9 rounded-16 overflow-hidden mb-20"><iframe src="' . htmlspecialchars($iframe_src) . '" allowfullscreen frameborder="0"></iframe></div>';
                                            } else {
                                                echo '<div class="rounded-16 overflow-hidden mb-20"><video class="player w-100" controls><source src="' . htmlspecialchars($vurl) . '"></video></div>';
                                            }
                                        }
                                    ?>
                                <?php elseif ($lecture->lecture_type == 'audio' && $lecture->content_url): ?>
                                    <div class="mb-20">
                                        <audio controls class="w-100">
                                            <source src="<?php echo htmlspecialchars($lecture->content_url); ?>">
                                        </audio>
                                    </div>
                                <?php elseif ($lecture->lecture_type == 'pptx_embed' && $lecture->content_url): ?>
                                    <div class="text-center mb-20">
                                        <a href="<?php echo BASE_URL . htmlspecialchars($lecture->content_url); ?>" target="_blank" class="btn btn-main rounded-pill">
                                            <i class="ph ph-presentation-chart me-2"></i>View Presentation
                                        </a>
                                    </div>
                                <?php elseif ($lecture->lecture_type == 'file' && $lecture->content_url): ?>
                                    <div class="text-center mb-20">
                                        <?php
                                        $file_extension = strtolower(pathinfo($lecture->content_url, PATHINFO_EXTENSION));
                                        if ($file_extension == 'pdf'):
                                        ?>
                                            <iframe src="<?php echo htmlspecialchars($lecture->content_url); ?>" width="100%" height="800px" style="border: none; border-radius: 8px;"></iframe>
                                        <?php else: ?>
                                            <a href="<?php echo htmlspecialchars($lecture->content_url); ?>" class="btn btn-main rounded-pill" download>
                                                <i class="ph ph-download-simple me-2"></i>Download File (<?php echo htmlspecialchars($lecture->file_name); ?>)
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-16 fw-bold">Lecture Details</h5>
                        
                        <div class="mb-16">
                            <p class="text-gray-600 mb-4 text-13"><strong>Type:</strong></p>
                            <span class="py-4 px-12 bg-main-50 text-main-600 rounded-pill text-13">
                                <?php echo $is_multiple_format ? 'Multiple Formats' : ucfirst($lecture->lecture_type); ?>
                            </span>
                        </div>
                        
                        <?php if ($lecture->duration_minutes): ?>
                        <div class="mb-16 pb-16 border-bottom border-gray-100">
                            <p class="text-gray-600 mb-4 text-13"><strong>Duration:</strong></p>
                            <p class="text-15"><?php echo $lecture->duration_minutes; ?> minutes</p>
                        </div>
                        <?php endif; ?>

                        <div class="mb-16 pb-16 border-bottom border-gray-100">
                            <p class="text-gray-600 mb-4 text-13"><strong>Order:</strong></p>
                            <p class="text-15"><?php echo $lecture->lecture_order; ?></p>
                        </div>

                        <?php if ($is_multiple_format && !empty($lecture_files)): ?>
                        <div class="mb-16 pb-16 border-bottom border-gray-100">
                            <p class="text-gray-600 mb-8 text-13"><strong>Available Formats:</strong></p>
                            <div class="flex-column gap-8">
                                <?php foreach ($lecture_files as $file): ?>
                                    <div class="flex-align gap-8">
                                        <i class="ph 
                                            <?php 
                                            echo $file['file_type'] == 'pdf' ? 'ph-file-pdf text-danger' : 
                                                 ($file['file_type'] == 'pptx' ? 'ph-presentation text-warning' : 
                                                 ($file['file_type'] == 'video' ? 'ph-video-camera text-success' : 
                                                 ($file['file_type'] == 'audio' ? 'ph-speaker-high text-primary' : 
                                                 'ph-article text-info')));
                                            ?>
                                        "></i>
                                        <span class="text-13"><?php echo ucfirst($file['file_type']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/js/boostrap.bundle.min.js"></script>
<script src="assets/js/phosphor-icon.js"></script>
<script src="assets/js/plyr.js"></script>
<script src="assets/js/main.js"></script>

<script>
    // Format selector for view page
    function switchFormat(lectureId, format) {
        // Hide all format contents
        document.querySelectorAll('.format-content').forEach(el => {
            el.style.display = 'none';
        });
        
        // Show selected format content
        const selectedContent = document.getElementById('format-' + format);
        if (selectedContent) {
            selectedContent.style.display = 'block';
        }
        
        // Update button states
        document.querySelectorAll('.format-btn').forEach(btn => {
            btn.classList.remove('btn-main');
            btn.classList.add('btn-outline-main');
        });
        
        const activeBtn = document.querySelector(`[onclick="switchFormat(${lectureId}, '${format}')"]`);
        if (activeBtn) {
            activeBtn.classList.remove('btn-outline-main');
            activeBtn.classList.add('btn-main');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Plyr !== 'undefined') {
            const players = Array.from(document.querySelectorAll('.player')).map(p => new Plyr(p));
        }
    });
</script>

</body>
</html>
