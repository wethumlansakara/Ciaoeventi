<?php
http_response_code(404);
$pageTitle = "Page Not Found";
require_once __DIR__ . '/includes/header.php';
?>

<section class="py-40 text-center">
    <div class="container">
        <div class="max-w-md mx-auto">
            <h1 class="text-8xl font-bold text-gradient mb-4">404</h1>
            <h2 class="text-2xl font-semibold mb-6">Oops! Page Not Found</h2>
            <p class="text-secondary mb-8">
                The page you're looking for doesn't exist or has been moved.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home mr-2"></i> Go Home
                </a>
                <a href="events.php" class="btn btn-secondary">
                    <i class="fas fa-calendar-alt mr-2"></i> Browse Events
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>