<?php
$pageTitle = "Home - Discover Amazing Events";
require_once __DIR__ . '/includes/header.php';

// Get stats for hero section
$statsQuery = "SELECT 
    (SELECT COUNT(*) FROM events) as total_events,
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT SUM(likes_count) FROM events) as total_likes,
    (SELECT COUNT(DISTINCT location) FROM events WHERE location IS NOT NULL AND location != '') as total_locations";
$statsResult = $conn->query($statsQuery);
$stats = $statsResult ? $statsResult->fetch_assoc() : [];

// Get the actual numbers
$eventsCount = $stats['total_events'] ?? 0;
$usersCount = $stats['total_users'] ?? 0;
$venuesCount = $stats['total_locations'] ?? 0;
$totalLikes = $stats['total_likes'] ?? 0;

// ALWAYS show numbers (not text labels) - like the original design
$eventsDisplay = $eventsCount; // Just show the number
$usersDisplay = $usersCount;   // Just show the number
$venuesDisplay = $venuesCount > 0 ? $venuesCount : '100+'; // Show number or "100+"

// Calculate rating: if no events, show 4.9; otherwise calculate from likes
if ($eventsCount == 0) {
    $ratingDisplay = "4.9";
} else {
    $avgLikesPerEvent = $eventsCount > 0 ? $totalLikes / $eventsCount : 0;
    $ratingValue = 4.5 + ($avgLikesPerEvent * 0.1);
    $ratingDisplay = number_format(min($ratingValue, 5.0), 1);
}

// Fetch featured/upcoming events from database
$query = "SELECT e.*, u.username FROM events e 
          JOIN users u ON e.user_id = u.id 
          WHERE e.event_date >= CURDATE() 
          ORDER BY e.event_date ASC 
          LIMIT 6";

$result = $conn->query($query);
$featuredEvents = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <div class="hero-badge animate-fade-up">
            <i class="fas fa-star"></i>
            <span>#1 Event Platform</span>
        </div>
        
        <h1 class="hero-title animate-fade-up delay-100">
            <span class="text-gradient">Unforgettable Moments</span>
        </h1>
        
        <p class="hero-subtitle animate-fade-up delay-200">
            Join the ultimate event discovery platform. Find parties, concerts, festivals, 
            and exclusive gatherings happening around you.
        </p>
        
        <div class="hero-buttons animate-fade-up delay-300">
            <a href="events.php" class="btn btn-primary btn-lg">
                <i class="fas fa-search"></i> Explore Events
            </a>
            <a href="create_event.php" class="btn btn-secondary btn-lg">
                <i class="fas fa-plus"></i> Host an Event
            </a>
        </div>
        
        <div class="hero-stats animate-fade-up delay-400">
            <div class="hero-stat">
                <div class="hero-stat-value"><?php echo $eventsDisplay; ?></div>
                <div class="hero-stat-label">Events</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-value"><?php echo $usersDisplay; ?></div>
                <div class="hero-stat-label">Users</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-value"><?php echo $venuesDisplay; ?></div>
                <div class="hero-stat-label">Venues</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-value"><?php echo $ratingDisplay; ?></div>
                <div class="hero-stat-label">Rating</div>
            </div>
        </div>
    </div>
</section>



<!-- Featured Events Section -->
<section class="py-20 bg-dark">
    <div class="container">
        <div class="text-center mb-12">
            <h2 class="text-2xl font-bold mb-2">
                <span class="text-gradient">Upcoming Events</span>
            </h2>
            <p class="text-secondary">Don't miss these amazing gatherings</p>
        </div>
        
        <?php if (!empty($featuredEvents)): ?>
            <div class="grid-3">
                <?php foreach($featuredEvents as $event): ?>
                <div class="card event-card animate-on-scroll" data-animate="fade-up">
                    <div class="card-image">
                        <img src="<?php echo !empty($event['banner_image']) ? 'uploads/' . $event['banner_image'] : 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'; ?>" 
                             alt="<?php echo htmlspecialchars($event['title']); ?>">
                        <div class="card-badge"><?php echo $event['category']; ?></div>
                        <?php if (isLoggedIn()): ?>
                        <button class="card-like" 
                            data-event-id="<?php echo $event['id']; ?>"
                            onclick="return handleLike(<?php echo $event['id']; ?>)">
                            <i class="fas fa-heart"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                        <p class="card-description"><?php echo htmlspecialchars(substr($event['description'], 0, 100)) . '...'; ?></p>
                        
                        <div class="card-meta">
                            <div class="card-meta-item">
                                <i class="fas fa-calendar"></i>
                                <span><?php echo date('M d, Y', strtotime($event['event_date'])); ?></span>
                            </div>
                            
                            <div class="card-meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($event['location']); ?></span>
                            </div>
                            <div class="card-meta-item">
                                <i class="fas fa-user"></i>
                                <span>@<?php echo htmlspecialchars($event['username']); ?></span>
                            </div>
                        </div>
                        
                        <div class="card-meta-item">
    <i class="fas fa-clock"></i>
    <span>
        <?php
        if (!empty($event['created_at']) && $event['created_at'] != '0000-00-00 00:00:00') {
            $created = new DateTime($event['created_at']);
            $now = new DateTime();
            $diff = $now->diff($created);
            
            if ($diff->days == 0) echo 'Today';
            elseif ($diff->days == 1) echo 'Yesterday';
            elseif ($diff->days < 7) echo $diff->days . ' days ago';
            else echo date('M j', strtotime($event['created_at']));
        } else {
            echo 'Recently';
        }
        ?>
    </span>
</div>
                        <div class="flex-between mt-4">
                            <div class="flex items-center">
                                <span class="text-gold font-semibold">
                                    <?php echo number_format($event['likes_count']); ?>
                                </span>
                                <span class="text-muted ml-1">
                                    like<?php echo $event['likes_count'] != 1 ? 's' : ''; ?>
                                </span>
                            </div>
                            <a href="events.php" class="btn btn-primary btn-sm">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-10 glass-card">
                <i class="fas fa-calendar-plus text-4xl text-secondary mb-4"></i>
                <h3 class="text-xl font-semibold mb-2">No upcoming events</h3>
                <p class="text-secondary mb-4">Be the first to create an amazing event!</p>
                <?php if (isLoggedIn()): ?>
                    <a href="create_event.php" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i> Create Event
                    </a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary">
                        <i class="fas fa-user-plus mr-2"></i> Sign Up to Create Events
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($featuredEvents) && count($featuredEvents) >= 6): ?>
            <div class="text-center mt-10">
                <a href="events.php" class="btn btn-secondary">
                    <i class="fas fa-calendar-alt mr-2"></i> View All Events
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>