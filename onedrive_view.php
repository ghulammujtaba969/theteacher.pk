<?php

$pptx_embed_url = "https://jimq-my.sharepoint.com/personal/gm_alvi_jimq_edu_pk/_layouts/15/Doc.aspx?sourcedoc={c2e4ca45-90c0-456d-92d7-dad69ec4d26a}&amp;action=embedview&amp;wdAr=1.7777777777777777&amp;wdEaaCheck=1";
define('APP_NAME', 'Syllabus Management System');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View PPTX - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Authorized PPTX Viewer</h4>
        <a class="btn btn-outline-secondary btn-sm" href="logout.php">Logout</a>
    </div>
    <div class="ratio ratio-16x9">
    <iframe src="<?php echo htmlspecialchars($pptx_embed_url); ?>" width="720px" height="480px" frameborder="0">This is an embedded <a target="_blank" href="https://office.com">Microsoft Office</a> presentation, powered by <a target="_blank" href="https://office.com/webapps">Office</a>.</iframe>
    </div>
    <p class="text-muted mt-2 mb-0">Signed in as 
        
    </p>
    <div class="mt-3">
        <a href="dashboard.php" class="btn btn-link">Back to Dashboard</a>
    </div>
    
</div>
</body>
</html>


