<?php
$pageTitle = "Create Event";
require_once __DIR__ . '/includes/header.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = $_POST['category'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = trim($_POST['location']);
    $venue = trim($_POST['venue']);
    $ticket_link = trim($_POST['ticket_link']);
    $user_id = getCurrentUserId();
    
    // Validate
    $errors = [];
    
    if (empty($title)) {
        $errors['title'] = "Title is required";
    }
    
    if (empty($description)) {
        $errors['description'] = "Description is required";
    }
    
    if (empty($event_date)) {
        $errors['event_date'] = "Event date is required";
    } elseif (strtotime($event_date) < strtotime('today')) {
        $errors['event_date'] = "Event date must be in the future";
    }
    
    if (empty($location)) {
        $errors['location'] = "Location is required";
    }
    
    // Handle file upload
    $banner_image = null;
    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (in_array($_FILES['banner_image']['type'], $allowed_types)) {
            if ($_FILES['banner_image']['size'] <= $max_size) {
                $ext = pathinfo($_FILES['banner_image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '_' . time() . '.' . $ext;
                $upload_path = 'uploads/' . $filename;
                
                if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $upload_path)) {
                    $banner_image = $filename;
                } else {
                    $errors['banner_image'] = "Failed to upload image";
                }
            } else {
                $errors['banner_image'] = "Image must be less than 5MB";
            }
        } else {
            $errors['banner_image'] = "Only JPG, PNG, GIF, and WebP images are allowed";
        }
    }
    
    if (empty($errors)) {
        // Insert event
        $stmt = $conn->prepare("INSERT INTO events (title, description, category, event_date, event_time, 
                                 location, venue, banner_image, ticket_link, user_id) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssi", $title, $description, $category, $event_date, $event_time, 
                         $location, $venue, $banner_image, $ticket_link, $user_id);
        
        if ($stmt->execute()) {
            // Success
            header("Location: events.php?success=1");
            exit();
        } else {
            $errors['general'] = "Failed to create event. Please try again.";
        }
    }
}
?>

<section class="py-20">
    <div class="container">
        <div class="max-w-2xl mx-auto">
            <div class="text-center mb-10">
                <h1 class="text-3xl font-extrabold mb-2">
                    <span class="text-gradient">Create Your Event</span>
                </h1>
                <p class="text-secondary">Share your amazing event with thousands of party lovers</p>
            </div>
            
            <div class="glass-card p-8">
                <?php if (isset($errors['general'])): ?>
                    <div class="mb-6 p-4 bg-red-900/20 border border-red-500/30 rounded">
                        <p class="text-red-400"><?php echo $errors['general']; ?></p>
                    </div>
                <?php endif; ?>
                
                <form id="event-form" class="event-form" method="POST" action="" enctype="multipart/form-data">
                    <div class="grid-2 gap-6">
                        <div class="form-group">
                            <label for="title" class="form-label">Event Title *</label>
                            <input type="text" id="title" name="title" class="form-input" 
                                   value="<?php echo $_POST['title'] ?? ''; ?>" required>
                            <?php if (isset($errors['title'])): ?>
                                <span class="form-error"><?php echo $errors['title']; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="category" class="form-label">Category *</label>
                            <select id="category" name="category" class="form-input form-select" required>
                                <option value="">Select Category</option>
                                <option value="Festival" <?php echo ($_POST['category'] ?? '') == 'Festival' ? 'selected' : ''; ?>>Festival</option>
                                <option value="Party" <?php echo ($_POST['category'] ?? '') == 'Party' ? 'selected' : ''; ?>>Party</option>
                                <option value="Concert" <?php echo ($_POST['category'] ?? '') == 'Concert' ? 'selected' : ''; ?>>Concert</option>
                                <option value="Nightlife" <?php echo ($_POST['category'] ?? '') == 'Nightlife' ? 'selected' : ''; ?>>Nightlife</option>
                                <option value="Social" <?php echo ($_POST['category'] ?? '') == 'Social' ? 'selected' : ''; ?>>Social</option>
                                <option value="Music" <?php echo ($_POST['category'] ?? '') == 'Music' ? 'selected' : ''; ?>>Music</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Event Description *</label>
                        <textarea id="description" name="description" class="form-input form-textarea" rows="4" required><?php echo $_POST['description'] ?? ''; ?></textarea>
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
                                   value="<?php echo $_POST['event_time'] ?? '20:00'; ?>" required>
                        </div>
                    </div>
                    
                    <div class="grid-2 gap-6">
                        <div class="form-group">
                            <label for="location" class="form-label">Location *</label>
                            <input type="text" id="location" name="location" class="form-input" 
                                   value="<?php echo $_POST['location'] ?? ''; ?>" required>
                            <?php if (isset($errors['location'])): ?>
                                <span class="form-error"><?php echo $errors['location']; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="venue" class="form-label">Venue Name</label>
                            <input type="text" id="venue" name="venue" class="form-input" 
                                   value="<?php echo $_POST['venue'] ?? ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="ticket_link" class="form-label">Ticket Link</label>
                        <input type="url" id="ticket_link" name="ticket_link" class="form-input" 
                               value="<?php echo $_POST['ticket_link'] ?? 'https://www.tickets.lk'; ?>">
                        <small class="text-muted">Link to your tickets.lk page or other ticketing platform</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="banner_image" class="form-label">Event Banner Image</label>
                        <input type="file" id="banner_image" name="banner_image" class="form-input" 
                               accept="image/*">
                        <?php if (isset($errors['banner_image'])): ?>
                            <span class="form-error"><?php echo $errors['banner_image']; ?></span>
                        <?php endif; ?>
                        <small class="text-muted">Recommended size: 1200x600px, Max: 5MB</small>
                    </div>
                    
                    <div class="mt-8">
                        <button type="submit" class="btn btn-primary btn-lg w-full">
                            <i class="fas fa-calendar-plus mr-2"></i> Create Event
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="mt-8 text-center">
                <p class="text-secondary">
                    <i class="fas fa-lightbulb text-gold mr-2"></i>
                    Tip: Add a catchy title and high-quality image to attract more attendees
                </p>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('event_date').min = new Date().toISOString().split('T')[0];
</script>

<?php require_once 'includes/footer.php'; ?>