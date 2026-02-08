<?php
require_once __DIR__ . '/includes/header.php';
requireLogin();

// Get event ID from URL
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($event_id <= 0) {
    header("Location: events.php");
    exit();
}

// Check if user owns the event
$stmt = $conn->prepare("SELECT id, banner_image FROM events WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $event_id, getCurrentUserId());
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    header("Location: events.php?error=notfound");
    exit();
}

// Handle delete
if (isset($_POST['confirm_delete'])) {
    // Delete banner image if exists
    if (!empty($event['banner_image']) && file_exists('uploads/' . $event['banner_image'])) {
        unlink('uploads/' . $event['banner_image']);
    }
    
    // Delete event from database
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $event_id, getCurrentUserId());
    
    if ($stmt->execute()) {
        // Also delete likes for this event
        $conn->query("DELETE FROM likes WHERE event_id = $event_id");
        
        header("Location: events.php?deleted=1");
        exit();
    } else {
        header("Location: events.php?error=deletefailed");
        exit();
    }
}
?>

<section class="py-20">
    <div class="container">
        <div class="max-w-md mx-auto">
            <div class="glass-card p-8 text-center">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-red-900/20 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-400 text-3xl"></i>
                </div>
                
                <h2 class="text-2xl font-bold mb-4">Delete Event</h2>
                <p class="text-secondary mb-6">
                    Are you sure you want to delete this event? This action cannot be undone.
                </p>
                
                <div class="space-y-4">
                    <form method="POST" action="">
                        <button type="submit" name="confirm_delete" 
                                class="btn bg-red-600 hover:bg-red-700 text-white w-full">
                            <i class="fas fa-trash mr-2"></i> Yes, Delete Event
                        </button>
                    </form>
                    
                    <a href="events.php" class="btn btn-secondary w-full">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>