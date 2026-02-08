<?php
$pageTitle = "Events";
require_once __DIR__ . '/includes/header.php';

// Get filters
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$query = "SELECT e.*, u.username FROM events e 
          JOIN users u ON e.user_id = u.id 
          WHERE 1=1";
$params = [];
$types = "";

if (!empty($category)) {
    $query .= " AND e.category = ?";
    $params[] = $category;
    $types .= "s";
}

if (!empty($search)) {
    // First, check if search term matches any category exactly
    $categories = ['Festival', 'Party', 'Concert', 'Nightlife', 'Social', 'Music'];
    
    // Normalize search term: make first letter uppercase, rest lowercase
    $normalizedSearch = ucfirst(strtolower($search));
    
    if (in_array($normalizedSearch, $categories)) {
        // If user typed a category name, search that category field exactly
        $query .= " AND (e.title LIKE ? OR e.description LIKE ? OR e.location LIKE ? OR e.category = ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;  // For title
        $params[] = $searchTerm;  // For description
        $params[] = $searchTerm;  // For location
        $params[] = $normalizedSearch;  // For category (exact match)
        $types .= "ssss";
    } else {
        // Normal search - don't include category field
        $query .= " AND (e.title LIKE ? OR e.description LIKE ? OR e.location LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;  // For title
        $params[] = $searchTerm;  // For description
        $params[] = $searchTerm;  // For location
        $types .= "sss";
    }
}

$query .= " ORDER BY e.event_date ASC";

// Prepare and execute
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$events = $stmt->get_result();
?>

<section class="py-20">
    <div class="container">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold mb-2">
                <span class="text-gradient">Discover Events</span>
            </h1>
            <p class="text-secondary">Find the perfect party, festival, or gathering</p>
        </div>
        
        <!-- Search and Filter -->
        <div class="glass-card p-6 mb-10">
            <form method="GET" action="" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-secondary"></i>
                        <input type="text" name="search" class="form-input pl-12" 
                               placeholder="Search events..." value="<?php echo htmlspecialchars($search); ?>">
                        <small class="text-secondary text-xs mt-1 ml-1 block">
                            <i class="fas fa-info-circle"></i> Search titles, descriptions, locations, or category names
                        </small>
                    </div>
                </div>
                
                <div class="w-full md:w-auto">
                    <select name="category" class="form-input form-select">
                        <option value="">All Categories</option>
                        <option value="Festival" <?php echo $category == 'Festival' ? 'selected' : ''; ?>>Festival</option>
                        <option value="Party" <?php echo $category == 'Party' ? 'selected' : ''; ?>>Party</option>
                        <option value="Concert" <?php echo $category == 'Concert' ? 'selected' : ''; ?>>Concert</option>
                        <option value="Nightlife" <?php echo $category == 'Nightlife' ? 'selected' : ''; ?>>Nightlife</option>
                        <option value="Social" <?php echo $category == 'Social' ? 'selected' : ''; ?>>Social</option>
                        <option value="Music" <?php echo $category == 'Music' ? 'selected' : ''; ?>>Music</option>
                    </select>
                </div>
                
                <div>
                    <button type="submit" class="btn btn-primary w-full md:w-auto">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                </div>
                
                <div>
                    <a href="events.php" class="btn btn-secondary w-full md:w-auto">
                        <i class="fas fa-redo mr-2"></i> Clear
                    </a>
                </div>
            </form>
            
            <!-- Show active filters -->
            <?php if (!empty($search) || !empty($category)): ?>
            <div class="mt-4 flex flex-wrap gap-2">
                <?php if (!empty($search)): ?>
                <span class="bg-neon-pink/20 text-neon-pink px-3 py-1 rounded-full text-sm">
                    <i class="fas fa-search mr-1"></i> Search: "<?php echo htmlspecialchars($search); ?>"
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['search' => ''])); ?>" class="ml-2 hover:opacity-80">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
                <?php endif; ?>
                
                <?php if (!empty($category)): ?>
                <span class="bg-gold/20 text-gold px-3 py-1 rounded-full text-sm">
                    <i class="fas fa-tag mr-1"></i> Category: <?php echo htmlspecialchars($category); ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['category' => ''])); ?>" class="ml-2 hover:opacity-80">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="mb-6 p-4 bg-green-900/20 border border-green-500/30 rounded">
                <p class="text-green-400">
                    <i class="fas fa-check-circle mr-2"></i>
                    Event created successfully!
                </p>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['edited'])): ?>
            <div class="mb-6 p-4 bg-green-900/20 border border-green-500/30 rounded">
                <p class="text-green-400">
                    <i class="fas fa-check-circle mr-2"></i>
                    Event updated successfully!
                </p>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['deleted'])): ?>
            <div class="mb-6 p-4 bg-blue-900/20 border border-blue-500/30 rounded">
                <p class="text-blue-400">
                    <i class="fas fa-info-circle mr-2"></i>
                    Event deleted successfully!
                </p>
            </div>
        <?php endif; ?>
        
        <!-- Events Grid -->
        <?php if ($events->num_rows > 0): ?>
            <div class="grid-3">
                <?php while($event = $events->fetch_assoc()): ?>
                <div class="card event-card animate-on-scroll" data-animate="fade-up">
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
                        <p class="card-description"><?php echo htmlspecialchars(substr($event['description'], 0, 100)) . '...'; ?></p>
                        
                        <div class="card-meta">
                            <div class="card-meta-item">
                                <i class="fas fa-calendar"></i>
                                <span><?php echo date('M d, Y', strtotime($event['event_date'])); ?> at <?php echo date('g:i A', strtotime($event['event_time'])); ?></span>
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
                                <span class="like-count text-gold font-semibold">
                                    <?php echo number_format($event['likes_count']); ?>
                                </span>
                                <span class="text-muted ml-1">
                                    like<?php echo $event['likes_count'] != 1 ? 's' : ''; ?>
                                </span>
                            </div>
                            
                            <div class="flex gap-2">
                                <?php if (isLoggedIn() && $event['user_id'] == getCurrentUserId()): ?>
                                    <a href="edit_event.php?id=<?php echo $event['id']; ?>" 
                                       class="btn btn-sm btn-secondary" title="Edit Event">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <a href="<?php echo $event['ticket_link'] ?? 'https://www.tickets.lk'; ?>" 
                                   target="_blank" class="btn btn-primary btn-sm">
                                    Get Tickets
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-16 glass-card">
                <i class="fas fa-calendar-times text-4xl text-secondary mb-4"></i>
                <h3 class="text-xl font-semibold mb-2">No events found</h3>
                <p class="text-secondary mb-6">
                    <?php echo empty($search) && empty($category) ? 
                        'Be the first to create an event!' : 
                        'Try adjusting your search filters.'; ?>
                </p>
                <?php if (isLoggedIn()): ?>
                    <a href="create_event.php" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i> Create First Event
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>