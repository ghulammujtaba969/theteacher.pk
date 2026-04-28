<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecture #N: Your Presentation Title Viewer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?php // include 'includes/styles.php'; // Uncomment if you want global styles ?>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container-fluid-custom {
            padding: 0 15px; /* Custom padding for the page content */
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .top-bar {
            background-color: #f8f9fa;
            padding: 10px 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 60px;
            flex-shrink: 0;
        }
        .iframe-wrapper {
            flex-grow: 1; /* Take up remaining height */
            padding: 15px 30px 30px 30px; /* Left, Right, Bottom margins for iframe */
            background-color: #e9ecef; /* Light background for contrast */
        }
        iframe {
            width: 100%;
            height: 100%; /* Fill the wrapper */
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h4.mb-0 {
            color: #343a40;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container-fluid-custom">
        <div class="top-bar">
            <h4 class="mb-0" id="lectureTitleDisplay">Lecture #N: Your Presentation Title</h4>
            <button type="button" class="btn btn-secondary btn-sm" onclick="history.back()">
                <i class="fas fa-arrow-left me-2"></i>Back
            </button>
        </div>
        <div class="iframe-wrapper">
            <!-- <iframe 
                src="https://jimq-my.sharepoint.com/:p:/g/personal/gm_alvi_jimq_edu_pk/ESNkScKiwl9KnEqGIkBkHt0BIlTblPMrreZAyMkKQsPr2g?e=0ylYC5&action=embedview&wdAr=1.7777777777777777&wdEaaCheck=1" 
                frameborder="0" 
                allowfullscreen
            >This is an embedded <a target="_blank" href="https://office.com">Microsoft Office</a> presentation, powered by <a target="_blank" href="https://office.com/webapps">Office</a>.</iframe> -->
            <iframe src="https://jimq-my.sharepoint.com/personal/gm_alvi_jimq_edu_pk/_layouts/15/Doc.aspx?sourcedoc={95d0289f-f35d-484c-ab1f-58b02dbcef6a}&amp;action=embedview&amp;wdAr=1.7777777777777777" width="476px" height="288px" frameborder="0">This is an embedded <a target="_blank" href="https://office.com">Microsoft Office</a> presentation, powered by <a target="_blank" href="https://office.com/webapps">Office</a>.</iframe>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
