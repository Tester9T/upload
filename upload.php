<?php
session_start();

// Create uploads directory if it doesn't exist
$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Create files database JSON if it doesn't exist
$dbFile = 'files_db.json';
if (!file_exists($dbFile)) {
    file_put_contents($dbFile, json_encode([]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo 'Upload failed with error code: ' . $file['error'];
        echo '<br><a href="index.php">Go Back</a>';
        exit;
    }
    
    // Get custom password from form FIRST
    $password = isset($_POST['custom_password']) ? trim($_POST['custom_password']) : '';
    
    // Validate password
    if (empty($password)) {
        echo 'Please provide a password!';
        echo '<br><a href="index.php">Go Back</a>';
        exit;
    }
    
    if (strlen($password) > 20) {
        echo 'Password must be 20 characters or less!';
        echo '<br><a href="index.php">Go Back</a>';
        exit;
    }
    
    // Get file information
    $originalName = basename($file['name']);
    $fileSize = $file['size'];
    $tmpName = $file['tmp_name'];
    
    // Generate unique file ID
    $fileId = uniqid('file_', true);
    $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
    $storedFileName = $fileId . '.' . $fileExtension;
    $filePath = $uploadDir . $storedFileName;
    
    // Move uploaded file
    if (move_uploaded_file($tmpName, $filePath)) {
        // Read existing database
        $dbContent = file_get_contents($dbFile);
        $filesDb = json_decode($dbContent, true);
        
        if ($filesDb === null) {
            $filesDb = [];
        }
        
        // Add new file information
        $filesDb[$fileId] = [
            'original_name' => $originalName,
            'stored_name' => $storedFileName,
            'size' => $fileSize,
            'password' => $password,
            'upload_date' => date('Y-m-d H:i:s'),
            'path' => $filePath
        ];
        
        // Save to database
        file_put_contents($dbFile, json_encode($filesDb, JSON_PRETTY_PRINT));
        
        // Get the folder name from current script location
        $scriptName = basename($_SERVER['PHP_SELF']);
        $fullPath = $_SERVER['PHP_SELF'];
        $folder = str_replace('/' . $scriptName, '', $fullPath);
        
        // Build the download URL
        $protocol = 'http';
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $protocol = 'https';
        }
        
        $downloadLink = $protocol . '://' . $_SERVER['HTTP_HOST'] . $folder . '/download.php?id=' . $fileId;
        
        // Store in session
        $_SESSION['download_link'] = $downloadLink;
        $_SESSION['password'] = $password;
        
        // Redirect to index with success flag
        header('Location: index.php?success=1');
        exit();
    } else {
        echo 'Failed to move uploaded file.';
        echo '<br>Temp file: ' . $tmpName;
        echo '<br>Destination: ' . $filePath;
        echo '<br>Upload directory exists: ' . (file_exists($uploadDir) ? 'Yes' : 'No');
        echo '<br>Upload directory writable: ' . (is_writable($uploadDir) ? 'Yes' : 'No');
        echo '<br><a href="index.php">Go Back</a>';
        exit;
    }
} else {
    header('Location: index.php');
    exit();
}
?>