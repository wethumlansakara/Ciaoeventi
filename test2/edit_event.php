<?php
$pageTitle = "Edit Event";
require_once __DIR__ . '/includes/header.php';
requireLogin();

// Get event ID from URL
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($event_id <= 0) {
    header("Location: events.php");
    exit();
}

// Get event details
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $event_id, getCurrentUserId());
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    // Event not found or user doesn't own it
    header("Location: events.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = $_POST['category'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = trim($_POST['location']);
    $venue = trim($_POST['venue']);
    $ticket_link = trim($_POST['ticket_link']);
    
    // Validate
    $errors = [];
    
    if (empty($title)) $errors['title'] = "Title is required";
    if (empty($description)) $errors['description'] = "Description is required";
    if (empty($event_date)) $errors['event_date'] = "Event date is required";
    if (empty($location)) $errors['location'] = "Location is required";
    
    // Handle file upload
    $banner_image = $event['banner_image']; // Keep existing if not changed
    
    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (in_array($_FILES['banner_image']['type'], $allowed_types)) {
            if ($_FILES['banner_image']['size'] <= $max_size) {
                $ext = pathinfo($_FILES['banner_image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '_' . time() . '.' . $ext;
                $upload_path = 'uploads/' . $filename;
                
                if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $upload_path)) {
                    // Delete old image if exists
                    if (!empty($event['banner_image']) && file_exists('uploads/' . $event['banner_image'])) {
                        unlink('uploads/' . $event['banner_image']);
                    }
                    $banner_image = $filename;
                }
            } else {
                $errors['banner_image'] = "Image must be less than 5MB";
            }
        } else {
            $errors['banner_image'] = "Only JPG, PNG, GIF, and WebP images are allowed";
        }
    }
    
    if (empty($errors)) {
        // Update event
        $stmt = $conn->prepare("UPDATE events SET 
            title = ?, description = ?, category = ?, event_date = ?, event_time = ?,
            location = ?, venue = ?, banner_image = ?, ticket_link = ?, status = 'edited'
            WHERE id = ? AND user_id = ?");
        
        $stmt->bind_param("sssssssssii", 
            $title, $description, $category, $event_date, $event_time,
            $location, $venue, $banner_image, $ticket_link, $event_id, getCurrentUserId());
        
        if ($stmt->execute()) {
            // Success - redirect to events page
            header("Location: events.php?edited=1");
            exit();
        } else {
            $errors['general'] = "Failed to update event. Please try again.";
        }
    }
}
?>

<section class="py-20">
    <div class="container">
        <div class="max-w-2xl mx-auto">
            <div class="text-center mb-10">
                <h1 class="text-3xl font-extrabold mb-2">
                    <span class="text-gradient">Edit Your Event</span>
                </h1>
                <p class="text-secondary">Update your event details</p>
            </div>
            
            <div class="glass-card p-8">
                <?php if (isset($errors['general'])): ?>
                    <div class="mb-6 p-4 bg-red-900/20 border border-red-500/30 rounded">
                        <p class="text-red-400"><?php echo $errors['general']; ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['success'])): ?>
                    <div class="mb-6 p-4 bg-green-900/20 border border-green-500/30 rounded">
                        <p class="text-green-400">
                            <i class="fas fa-check-circle mr-2"></i>
                            Event updated successfully!
                        </p>
                    </div>
                <?php endif; ?>
                
                <form id="edit-event-form" method="POST" action="" enctype="multipart/form-data">
                    <div class="grid-2 gap-6">
                        <div class="form-group">
                            <label for="title" class="form-label">Event Title *</label>
                            <input type="text" id="title" name="title" class="form-input" 
                                   value="<?php echo htmlspecialchars($event['title']); ?>" required>
                            <?php if (isset($errors['title'])): ?>
                                <span class="form-error"><?php echo $errors['title']; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="category" class="form-label">Category *</label>
                            <select id="category" name="category" class="form-input form-select" required>
                                <option value="Festival" <?php echo $event['category'] == 'Festival' ? 'selected' : ''; ?>>Festival</option>
                                <option value="Party" <?php echo $event['category'] == 'Party' ? 'selected' : ''; ?>>Party</option>
                                <option value="Concert" <?php echo $event['category'] == 'Concert' ? 'selected' : ''; ?>>Concert</option>
                                <option value="Nightlife" <?php echo $event['category'] == 'Nightlife' ? 'selected' : ''; ?>>Nightlife</option>
                                <option value="Social" <?php echo $event['category'] == 'Social' ? 'selected' : ''; ?>>Social</option>
                                <option value="Music" <?php echo $event['category'] == 'Music' ? 'selected' : ''; ?>>Music</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Event Description *</label>
                        <textarea id="description" name="description" class="form-input form-textarea" rows="4" required><?php echo htmlspecialchars($event['description']); ?></textarea>
                        <?php if (isset($errors['description'])): ?>
                            <span class="form-error"><?php echo $errors['description']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="grid-2 gap-6">
                        <div class="form-group">
    <label for="event_date" class="form-label">Event Date *</label>
    <input type="date" id="event_date" name="event_date" class="form-input" 
           value="<?php echo $_POST['event_date'] ?? ''; ?>" 
           min="<?php echo date('Y-m-d'); ?>" required>
    <?php if (isset($errors['event_date'])): ?>
        <span class="form-error"><?php echo $errors['event_date']; ?></span>
    <?php endif; ?>
</div>
                        
                        <div class="form-group">
                            <label for="event_time" class="form-label">Event Time *</label>
                            <input type="time" id="event_time" name="event_time" class="form-input" 
                                   value="<?php echo $event['event_time']; ?>" required>
                        </div>
                    </div>
                    
                    <div class="grid-2 gap-6">
                        <div class="form-group">
                            <label for="location" class="form-label">Location *</label>
                            <input type="text" id="location" name="location" class="form-input" 
                                   value="<?php echo htmlspecialchars($event['location']); ?>" required>
                            <?php if (isset($errors['location'])): ?>
                                <span class="form-error"><?php echo $errors['location']; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="venue" class="form-label">Venue Name</label>
                            <input type="text" id="venue" name="venue" class="form-input" 
                                   value="<?php echo htmlspecialchars($event['venue'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="ticket_link" class="form-label">Ticket Link</label>
                        <input type="url" id="ticket_link" name="ticket_link" class="form-input" 
                               value="<?php echo htmlspecialchars($event['ticket_link'] ?? 'https://www.tickets.lk'); ?>">
                        <small class="text-muted">Link to your tickets.lk page or other ticketing platform</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="banner_image" class="form-label">Event Banner Image</label>
                        <div class="mb-4">
                            <?php if (!empty($event['banner_image'])): ?>
                                <div class="mb-2">
                                    <p class="text-sm text-secondary mb-1">Current Image:</p>
                                    <img src="uploads/<?php echo htmlspecialchars($event['banner_image']); ?>" 
                                         alt="Current banner" class="w-48 h-32 object-cover rounded-lg">
                                </div>
                            <?php endif; ?>
                            <input type="file" id="banner_image" name="banner_image" class="form-input" 
                                   accept="image/*">
                            <?php if (isset($errors['banner_image'])): ?>
                                <span class="form-error"><?php echo $errors['banner_image']; ?></span>
                            <?php endif; ?>
                            <small class="text-muted">Leave empty to keep current image. Max: 5MB</small>
                        </div>
                    </div>
                    
                    <div class="flex gap-4 mt-8">
                        <button type="submit" class="btn btn-primary flex-1">
                            <i class="fas fa-save mr-2"></i> Update Event
                        </button>
                        <a href="events.php" class="btn btn-secondary">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
            
            <div class="mt-8">
                <div class="glass-card p-6">
                    <h3 class="text-lg font-semibold mb-4">Event Statistics</h3>
                    <div class="grid-2 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gold"><?php echo number_format($event['likes_count']); ?></div>
                            <div class="text-sm text-secondary">Likes</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-semibold">
                                <?php echo date('F j, Y', strtotime($event['created_at'])); ?>
                            </div>
                            <div class="text-sm text-secondary">Created On</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Set min date to today
document.getElementById('event_date').min = new Date().toISOString().split('T')[0];
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>