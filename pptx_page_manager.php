<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_permission('pptx.manage', 'dashboard.php');

// config.php - Configuration file
define('BASE_DIR', 'lectures');
define('ALLOWED_EXTENSIONS', ['php']);

// Create base lectures directory if it doesn't exist
if (!is_dir(BASE_DIR)) {
    mkdir(BASE_DIR, 0755, true);
}

// Helper Functions
function sanitizeFileName($filename) {
    return preg_replace('/[^a-zA-Z0-9_-]/', '', $filename);
}

function sanitizeDirName($dirname) {
    return preg_replace('/[^a-zA-Z0-9_-]/', '', $dirname);
}

function getDirectories($path = BASE_DIR) {
    $directories = [];
    if (is_dir($path)) {
        $items = scandir($path);
        foreach ($items as $item) {
            if ($item != '.' && $item != '..' && is_dir($path . '/' . $item)) {
                $directories[] = $item;
            }
        }
    }
    return $directories;
}

function getFiles($directory) {
    $files = [];
    $path = BASE_DIR . '/' . $directory;
    if (is_dir($path)) {
        $items = scandir($path);
        foreach ($items as $item) {
            if ($item != '.' && $item != '..' && is_file($path . '/' . $item)) {
                $files[] = $item;
            }
        }
    }
    return $files;
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_directory':
                $dirName = sanitizeDirName($_POST['dir_name']);
                if (!empty($dirName)) {
                    $dirPath = BASE_DIR . '/' . $dirName;
                    if (!is_dir($dirPath)) {
                        if (mkdir($dirPath, 0755)) {
                            echo json_encode(['success' => true, 'message' => 'Directory created successfully!']);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Failed to create directory!']);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Directory already exists!']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid directory name!']);
                }
                exit;
                
            case 'create_file':
                $pageName = sanitizeFileName($_POST['page_name']);
                $htmlTemplate = $_POST['html_template'];
                $selectedDir = sanitizeDirName($_POST['selected_dir']);
                
                if (!empty($pageName) && !empty($selectedDir)) {
                    $dirPath = BASE_DIR . '/' . $selectedDir;
                    if (is_dir($dirPath)) {
                        $fileName = $pageName . '.php';
                        $filePath = $dirPath . '/' . $fileName;
                        
                        if (!file_exists($filePath)) {
                            $phpContent =  $htmlTemplate;
                            
                            if (file_put_contents($filePath, $phpContent)) {
                                echo json_encode(['success' => true, 'message' => 'PHP file created successfully!']);
                            } else {
                                echo json_encode(['success' => false, 'message' => 'Failed to create file!']);
                            }
                        } else {
                            echo json_encode(['success' => false, 'message' => 'File already exists!']);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Selected directory does not exist!']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Page name and directory are required!']);
                }
                exit;
                
            case 'delete_file':
                $fileName = $_POST['file_name'];
                $directory = sanitizeDirName($_POST['directory']);
                $filePath = BASE_DIR . '/' . $directory . '/' . $fileName;
                
                if (file_exists($filePath)) {
                    if (unlink($filePath)) {
                        echo json_encode(['success' => true, 'message' => 'File deleted successfully!']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to delete file!']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'File does not exist!']);
                }
                exit;
                
            case 'delete_directory':
                $dirName = sanitizeDirName($_POST['dir_name']);
                $dirPath = BASE_DIR . '/' . $dirName;
                
                if (is_dir($dirPath)) {
                    // Check if directory is empty
                    $files = array_diff(scandir($dirPath), ['.', '..']);
                    if (empty($files)) {
                        if (rmdir($dirPath)) {
                            echo json_encode(['success' => true, 'message' => 'Directory deleted successfully!']);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Failed to delete directory!']);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Directory is not empty!']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Directory does not exist!']);
                }
                exit;
                
            case 'get_file_content':
                $fileName = $_POST['file_name'];
                $directory = sanitizeDirName($_POST['directory']);
                $filePath = BASE_DIR . '/' . $directory . '/' . $fileName;
                
                if (file_exists($filePath)) {
                    $content = file_get_contents($filePath);
                    echo json_encode(['success' => true, 'content' => $content]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'File does not exist!']);
                }
                exit;
        }
    }
}

$directories = getDirectories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP File & Directory Manager</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: white;
            min-height: 100vh;
            padding: 10px; /* Reduced padding for smaller screens */
            color: #333;
        }
        
        .container {
            width: 100%;
            max-width: 1200px; /* Max width for larger screens */
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: 1px solid #e9ecef;
            overflow: hidden;
        }
        
        .header {
            background: #2c3e50;
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            padding: 30px;
        }
        
        .section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 25px;
            border: 1px solid #e9ecef;
        }
        
        .section h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.5em;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        input[type="text"], select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
            background: white;
        }
        
        input[type="text"]:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #3498db;
        }
        
        textarea {
            height: 200px;
            resize: vertical;
            font-family: 'Courier New', monospace;
        }
        
        button {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s, transform 0.2s;
            width: 100%;
        }
        
        button:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .btn-danger {
            background: #e74c3c;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .file-list {
            background: white;
            border-radius: 5px;
            margin-top: 20px;
            border: 1px solid #e9ecef;
        }
        
        .file-list h3 {
            background: #f8f9fa;
            padding: 15px;
            margin: 0;
            border-bottom: 1px solid #e9ecef;
            color: #2c3e50;
        }
        
        .directory-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .directory-item:hover {
            background-color: #f8f9fa;
        }
        
        .directory-item:last-child {
            border-bottom: none;
        }
        
        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            border-bottom: 1px solid #f1f3f4;
        }
        
        .file-item:last-child {
            border-bottom: none;
        }
        
        .file-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
            width: auto;
        }
        
        .directory-files {
            display: none;
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            margin-top: 10px;
        }
        
        .full-width {
            grid-column: 1 / -1;
        }
        
        .template-examples {
            margin-top: 15px;
            padding: 15px;
            background: #e8f5e8;
            border-radius: 5px;
        }
        
        .template-examples h4 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .template-examples button {
            width: auto;
            margin: 5px;
            padding: 8px 12px;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .content {
                grid-template-columns: 1fr;
                gap: 20px;
                padding: 20px;
            }
            
            .header h1 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🗂️ PHP File & Directory Manager</h1>
            <p>Create directories and PHP files with HTML templates</p>
        </div>
        
        <div class="content">
            <!-- Directory Creation Section -->
            <div class="section">
                <h2>📁 Create Directory</h2>
                <div class="alert alert-success" id="dirAlert"></div>
                <div class="alert alert-error" id="dirError"></div>
                
                <form id="createDirForm">
                    <div class="form-group">
                        <label for="dirName">Directory Name:</label>
                        <input type="text" id="dirName" name="dir_name" placeholder="Enter directory name" required>
                    </div>
                    <button type="submit">Create Directory</button>
                </form>
            </div>
            
            <!-- File Creation Section -->
            <div class="section">
                <h2>📄 Create PHP File</h2>
                <div class="alert alert-success" id="fileAlert"></div>
                <div class="alert alert-error" id="fileError"></div>
                
                <form id="createFileForm">
                    <div class="form-group">
                        <label for="pageName">Page Name:</label>
                        <input type="text" id="pageName" name="page_name" placeholder="Enter page name (without .php)" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="selectedDir">Select Directory:</label>
                        <select id="selectedDir" name="selected_dir" required>
                            <option value="">-- Select Directory --</option>
                            <?php foreach ($directories as $dir): ?>
                                <option value="<?php echo htmlspecialchars($dir); ?>"><?php echo htmlspecialchars($dir); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="htmlTemplate">HTML Template:</label>
                        <textarea id="htmlTemplate" name="html_template" placeholder="Enter your HTML template here..." required></textarea>
                    </div>
                    
                    <div class="template-examples">
                        <h4>Quick Templates:</h4>
                        <button type="button" onclick="useTemplate('basic')">Basic HTML</button>
                        <button type="button" onclick="useTemplate('bootstrap')">Bootstrap</button>
                        <button type="button" onclick="useTemplate('form')">Contact Form</button>
                    </div>
                    
                    <button type="submit">Create PHP File</button>
                </form>
            </div>
            
            <!-- Directory and File Browser -->
            <div class="section full-width">
                <h2>📂 Directory & File Browser</h2>
                <div class="file-list">
                    <h3>Directories in "lectures" folder:</h3>
                    <div id="directoryList">
                        <?php if (empty($directories)): ?>
                            <div class="directory-item">No directories found. Create one above!</div>
                        <?php else: ?>
                            <?php foreach ($directories as $dir): ?>
                                <div class="directory-item" onclick="toggleDirectory('<?php echo htmlspecialchars($dir); ?>')">
                                    <strong>📁 <?php echo htmlspecialchars($dir); ?></strong>
                                    <button class="btn-danger btn-small" onclick="event.stopPropagation(); deleteDirectory('<?php echo htmlspecialchars($dir); ?>')">Delete Dir</button>
                                </div>
                                <div id="files-<?php echo htmlspecialchars($dir); ?>" class="directory-files">
                                    <h4>Files in <?php echo htmlspecialchars($dir); ?>:</h4>
                                    <?php 
                                    $files = getFiles($dir);
                                    if (empty($files)): ?>
                                        <div class="file-item">No files in this directory</div>
                                    <?php else: ?>
                                        <?php foreach ($files as $file): ?>
                                            <div class="file-item">
                                                <span>📄 <?php echo htmlspecialchars($file); ?></span>
                                                <div class="file-actions">
                                                    <button class="btn-small" onclick="viewFile('<?php echo htmlspecialchars($dir); ?>', '<?php echo htmlspecialchars($file); ?>')">View</button>
                                                    <button class="btn-danger btn-small" onclick="deleteFile('<?php echo htmlspecialchars($dir); ?>', '<?php echo htmlspecialchars($file); ?>')">Delete</button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Template examples
        const templates = {
        }
        
        function useTemplate(type) {
            document.getElementById('htmlTemplate').value = templates[type];
        }
        
        // Show/Hide alerts
        function showAlert(elementId, message, isSuccess) {
            const alert = document.getElementById(elementId);
            alert.textContent = message;
            alert.className = isSuccess ? 'alert alert-success' : 'alert alert-error';
            alert.style.display = 'block';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 3000);
        }
        
        // Create Directory
        document.getElementById('createDirForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('action', 'create_directory');
            formData.append('dir_name', document.getElementById('dirName').value);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(result) {
                if (result.success) {
                    showAlert('dirAlert', result.message, true);
                    document.getElementById('dirName').value = '';
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('dirError', result.message, false);
                }
            })
            .catch(function(error) {
                showAlert('dirError', 'An error occurred: ' + error.message, false);
            });
        });
        
        // Create File
        document.getElementById('createFileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('action', 'create_file');
            formData.append('page_name', document.getElementById('pageName').value);
            formData.append('selected_dir', document.getElementById('selectedDir').value);
            formData.append('html_template', document.getElementById('htmlTemplate').value);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(result) {
                if (result.success) {
                    showAlert('fileAlert', result.message, true);
                    document.getElementById('createFileForm').reset();
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('fileError', result.message, false);
                }
            })
            .catch(function(error) {
                showAlert('fileError', 'An error occurred: ' + error.message, false);
            });
        });
        
        // Toggle directory files view
        function toggleDirectory(dirName) {
            const filesDiv = document.getElementById('files-' + dirName);
            if (filesDiv.style.display === 'block') {
                filesDiv.style.display = 'none';
            } else {
                filesDiv.style.display = 'block';
            }
        }
        
        // Delete file
        function deleteFile(directory, fileName) {
            if (!confirm('Are you sure you want to delete this file?')) return;
            
            const formData = new FormData();
            formData.append('action', 'delete_file');
            formData.append('directory', directory);
            formData.append('file_name', fileName);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(result) {
                if (result.success) {
                    alert(result.message);
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(function(error) {
                alert('An error occurred: ' + error.message);
            });
        }
        
        // Delete directory
        function deleteDirectory(dirName) {
            if (!confirm('Are you sure you want to delete this directory? It must be empty.')) return;
            
            const formData = new FormData();
            formData.append('action', 'delete_directory');
            formData.append('dir_name', dirName);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(result) {
                if (result.success) {
                    alert(result.message);
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(function(error) {
                alert('An error occurred: ' + error.message);
            });
        }
        
        // View file content
        function viewFile(directory, fileName) {
            const formData = new FormData();
            formData.append('action', 'get_file_content');
            formData.append('directory', directory);
            formData.append('file_name', fileName);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(result) {
                if (result.success) {
                    // Open in new window/tab
                    const newWindow = window.open('', '_blank');
                    const content = result.content.replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    newWindow.document.write('<pre style="padding: 20px; font-family: monospace; background: #f8f9fa;">' + content + '</pre>');
                    newWindow.document.title = fileName;
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(function(error) {
                alert('An error occurred: ' + error.message);
            });
        }
    </script>
</body>
</html>
