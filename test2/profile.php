<?php
$pageTitle = "My Profile";
require_once __DIR__ . '/includes/header.php';
requireLogin();

$user = getUserInfo($conn, getCurrentUserId());

// Get user's events
$stmt = $conn->prepare("SELECT * FROM events WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", getCurrentUserId());
$stmt->execute();
$userEvents = $stmt->get_result();

// Calculate total likes for user's events
$totalLikes = 0;
$eventsData = []; // Store events for later use

if ($userEvents->num_rows > 0) {
    while($event = $userEvents->fetch_assoc()) {
        $totalLikes += $event['likes_count'];
        $eventsData[] = $event; // Store for later display
    }
    $userEvents->close();
} else {
    $userEvents->close();
}
?>

<section class="py-20">
    <div class="container">
        <div class="max-w-4xl mx-auto">
            <!-- Profile Header -->
            <div class="glass-card p-8 mb-8">
                <div class="flex flex-col md:flex-row items-center gap-6">
                    <div class="w-24 h-24 rounded-full bg-gradient-to-r from-pink-500 to-yellow-500 flex items-center justify-center text-white text-3xl">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="text-center md:text-left">
                        <h1 class="text-2xl font-extrabold mb-2">@<?php echo htmlspecialchars($user['username']); ?></h1>
                        <p class="text-secondary mb-1"><?php echo htmlspecialchars($user['email']); ?></p>
                        <p class="text-secondary text-sm">
                            Member since <?php echo date('F j, Y', strtotime($user['created_at'])); ?>
                        </p>

                        <!-- Stats Section - USING INLINE STYLE FOR YELLOW -->
                        <div class="flex gap-6 mt-4 justify-center md:justify-start">
                            <div class="text-center">
                                <div class="text-2xl font-bold" style="color: #FFD700 !important;"> <!-- Pure gold/yellow -->
                                    <?php echo count($eventsData); ?>
                                </div>
                                <div class="text-sm text-secondary">Events</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold" style="color: #FFD700 !important;"> <!-- Pure gold/yellow -->
                                    <?php echo $totalLikes; ?>
                                </div>
                                <div class="text-sm text-secondary">Total Likes</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Events -->
            <h2 class="text-xl font-bold mb-6">My Events</h2>
            
            <?php if (!empty($eventsData)): ?>
                <div class="grid-3">
                    <?php foreach($eventsData as $event): ?>
                    <div class="card event-card">
                        <div class="card-image">
                            <img src="<?php echo !empty($event['banner_image']) ? 'uploads/' . $event['banner_image'] : 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'; ?>" 
                                 alt="<?php echo htmlspecialchars($event['title']); ?>">
                            <div class="card-badge"><?php echo $event['category']; ?></div>
                            <button class="card-like" 
                                data-event-id="<?php echo $event['id']; ?>"
                                onclick="return handleLike(<?php echo $event['id']; ?>)">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                            <p class="card-description"><?php echo htmlspecialchars(substr($event['description'], 0, 80)) . '...'; ?></p>
                            
                            <div class="card-meta">
                                <div class="card-meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <span><?php echo date('M d, Y', strtotime($event['event_date'])); ?> at <?php echo date('g:i A', strtotime($event['event_time'])); ?></span>
                                </div>
                                <div class="card-meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($event['location']); ?></span>
                                </div>
                            </div>
                            
                            <div class="flex-between mt-4">
                                <div class="flex items-center">
                                    <span class="font-semibold" style="color: #FFD700 !important;"> <!-- Yellow like count -->
                                        <?php echo number_format($event['likes_count']); ?>
                                    </span>
                                    <span class="text-muted ml-1">
                                        like<?php echo $event['likes_count'] != 1 ? 's' : ''; ?>
                                    </span>
                                </div>
                                
                                <div class="flex gap-2">
                                    <a href="edit_event.php?id=<?php echo $event['id']; ?>" 
                                       class="btn btn-sm btn-secondary" title="Edit Event">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete_event.php?id=<?php echo $event['id']; ?>" 
                                       class="btn btn-sm bg-red-600 hover:bg-red-700 text-white" 
                                       title="Delete Event">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="glass-card p-8 text-center">
                    <i class="fas fa-calendar-plus text-4xl text-secondary mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">No events yet</h3>
                    <p class="text-secondary mb-6">Create your first event and share it with the community!</p>
                    <a href="create_event.php" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i> Create First Event
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
/* Force yellow color for all stats */
.profile-stat-number {
    color: #FFD700 !important;
    font-weight: bold;
    font-size: 1.5rem;
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>