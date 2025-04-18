<?php
// Start output buffering
ob_start();

// Set JSON header
header('Content-Type: application/json');

// Initialize response
$response = ['found' => false, 'path' => ''];

try {
    $filename = $_GET['filename'] ?? '';
    $type = $_GET['type'] ?? '';

    if (empty($filename)) {
        throw new Exception('Filename is required');
    }

    $root = __DIR__;
    $dirs = [];

    // Define common directories based on type
    switch ($type) {
        case 'css':
            $dirs = ['css', 'styles', 'assets/css'];
            break;
        case 'js':
            $dirs = ['js', 'scripts', 'assets/js'];
            break;
        case 'images':
            $dirs = ['images', 'img', 'assets/images'];
            break;
        case 'php':
            $dirs = ['includes', 'lib', 'src'];
            break;
        default:
            $dirs = [];
    }

    // Search in common directories
    foreach ($dirs as $dir) {
        $path = "$root/$dir/$filename";
        if (file_exists($path)) {
            $response['found'] = true;
            $response['path'] = realpath($path);
            ob_end_clean();
            echo json_encode($response);
            exit;
        }
    }

    // Search entire project (with limits)
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getFilename() === $filename) {
            $response['found'] = true;
            $response['path'] = $file->getPathname();
            ob_end_clean();
            echo json_encode($response);
            exit;
        }
    }

    // No file found
    $response['message'] = 'File not found';
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Find File Error: " . $e->getMessage());
}

// Clean output buffer and send JSON
ob_end_clean();
echo json_encode($response);
exit;
?>