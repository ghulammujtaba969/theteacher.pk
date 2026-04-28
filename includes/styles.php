<style>
    .sidebar-wrapper {
        background: linear-gradient(180deg, #2a2a72 0%, #009ffd 100%); /* Vibrant blue gradient */
        min-height: 100vh;
    }
    .sidebar {
        /* Background handled by sidebar-wrapper */
        min-height: 100vh;
        color: white; /* Ensure text is white for contrast */
    }
    
    /* Explicitly apply gradient to offcanvas sidebar content */
    .offcanvas-body.sidebar {
        background: linear-gradient(180deg, #2a2a72 0%, #009ffd 100%); /* Vibrant blue gradient */
    }

    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.8);
        padding: 12px 15px; /* Adjust padding */
        border-radius: 8px;
        margin: 5px 0;
        transition: all 0.3s ease-in-out; /* Smooth transitions */
        font-weight: 500; /* Slightly bolder */
    }
    .sidebar .nav-link:hover {
        background: rgba(255, 255, 255, 0.15); /* More visible hover */
        color: white;
        transform: translateX(5px); /* Subtle slide effect */
    }
    .sidebar .nav-link.active-link {
        background: rgba(255, 255, 255, 0.25); /* Stronger active state */
        color: white; /* Ensure text is white */
        font-weight: 700; /* Bold active link */
        border-left: 5px solid #fff; /* Highlight with a border */
        padding-left: 10px; /* Adjust padding for border */
    }
    .main-content {
        background: #f8f9fa;
        min-height: 100vh;
    }
    .content-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    /* Add a subtle animation for the dashboard cards */
    .stats-card {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }
    .stats-card:hover {
        transform: translateY(-7px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }
    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
    /* Responsive Adjustments */
    @media (max-width: 767.98px) { /* For screens smaller than md breakpoint */
        .sidebar-wrapper { /* Targets the offcanvas container */
            min-height: auto; /* Remove fixed height for mobile sidebar */
        }
        .sidebar { /* Targets the actual sidebar content inside offcanvas */
            min-height: 100%; /* Make it fill the offcanvas height */
        }
        .main-content {
            padding: 1rem !important; /* Reduce padding on small screens */
        }
    }
</style>
