<?php
require_once 'config/database.php';
$isLoggedIn = isLoggedIn();
$currentUser = $isLoggedIn ? getUserInfo($conn, getCurrentUserId()) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CiaoEventi - <?php echo $pageTitle ?? 'Event Discovery Platform'; ?></title>
    <!-- Meta Tags for Social Sharing -->
<meta property="og:title" content="CiaoEventi - <?php echo $pageTitle ?? 'Event Discovery Platform'; ?>">
<meta property="og:description" content="Discover amazing events, parties, festivals and gatherings near you.">
<meta property="og:image" content="https://images.unsplash.com/photo-1514525253161-7a46d19cd819?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80">
<meta property="og:url" content="http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
<meta property="og:type" content="website">
<meta name="twitter:card" content="summary_large_image">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=Space+Grotesk:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">

<link rel="icon" type="image/x-icon" href="12.png">    
    <style>
        /* Additional styles specific to this project */
        .event-card {
            transition: all 0.3s ease;
        }
        
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(255, 45, 122, 0.2);
        }
        
        .like-btn.liked i {
            color: var(--neon-pink);
            animation: heartbeat 0.6s ease;
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #ff2d7a 0%, #ff6b6b 50%, #ffd700 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="index.php" class="navbar-logo">
                <span class="gradient-text">CiaoEventi</span>
            </a>
            
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
            
            <ul class="nav-links">
                <li><a href="index.php" class="nav-link">Home</a></li>
                <li><a href="events.php" class="nav-link">Events</a></li>
                
               <?php if ($isLoggedIn): ?>
    <li><a href="create_event.php" class="nav-link">Create Event</a></li>
    <li><a href="profile.php" class="nav-link">
        <i class="fas fa-user mr-2"></i> <?php echo htmlspecialchars($currentUser['username']); ?>
    </a></li>
    <li><a href="logout.php" class="nav-link btn btn-sm btn-secondary">Logout</a></li>
<?php else: ?>
    <li><a href="login.php" class="nav-link btn btn-sm btn-secondary">Login</a></li>
    <li><a href="register.php" class="nav-link btn btn-sm btn-primary">Sign Up</a></li>
<?php endif; ?>
            </ul>
        </div>
    </nav>