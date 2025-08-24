<?php
// index.php
// This is the main file that loads the UI.
// It includes the header, sidebar, and the appropriate content page based on the URL.

// Get the requested page from the URL query parameter.
// Use a ternary operator to set a default page if none is specified.
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Define a mapping of page names to file paths to prevent directory traversal attacks.
$valid_pages = [
    'dashboard' => 'dashboard.php',
    'konkan-overview' => 'konkan-overview.php',
    'konkan-map' => 'konkan-map.php',
    'palghar' => 'palghar.php',
    'thane-rural' => 'thane-rural.php',
    'raigad' => 'raigad.php',
    'ratnagiri' => 'ratnagiri.php',
    'sindhudurg' => 'sindhudurg.php',
];

// Check if the requested page is in our list of valid pages.
if (!array_key_exists($page, $valid_pages)) {
    // If not, set the page to a default or show an error.
    $page = 'dashboard';
}

// Get the file path for the content page.
$content_file = $valid_pages[$page];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Tailwind CSS via CDN for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
    </style>
</head>
<body class="flex min-h-screen">
    <!-- Include the sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main content area -->
    <main class="flex-1 p-6 lg:p-12 overflow-y-auto">
        <?php
        // Include the selected content file.
        // The file is checked for validity above to ensure security.
        include $content_file;
        ?>
    </main>
</body>
</html>
