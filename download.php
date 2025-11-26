<?php
// Get file ID from URL
$fileId = isset($_GET['id']) ? $_GET['id'] : '';

// Load database
$dbFile = 'files_db.json';
if (!file_exists($dbFile)) {
    die('Database file not found.');
}

$filesDb = json_decode(file_get_contents($dbFile), true);

// Check if file exists in database
if (!isset($filesDb[$fileId])) {
    die('File not found.');
}

$fileInfo = $filesDb[$fileId];

// Handle download request
if (isset($_GET['action']) && $_GET['action'] === 'download') {
    $filePath = $fileInfo['path'];
    
    if (!file_exists($filePath)) {
        die('File not found on server.');
    }
    
    // Set headers for download
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $fileInfo['original_name'] . '"');
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    
    // Read and output file
    readfile($filePath);
    exit;
}

// Get file extension
$extension = strtolower(pathinfo($fileInfo['original_name'], PATHINFO_EXTENSION));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download - <?php echo htmlspecialchars($fileInfo['original_name']); ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .download-container {
            max-width: 380px;
            width: 100%;
            margin: 0 auto;
        }
        
        .download-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            animation: slideUp 0.5s ease-out;
        }
        
        .file-header {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 16px;
        }
        
        .file-icon-wrapper {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .file-icon-wrapper svg {
            width: 35px;
            height: 35px;
            color: #4b5563;
        }
        
        .file-info {
            flex: 1;
            min-width: 0;
        }
        
        .file-name {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            word-break: break-word;
            margin-bottom: 3px;
            line-height: 1.3;
        }
        
        .file-meta {
            font-size: 12px;
            color: #9ca3af;
            text-transform: uppercase;
        }
        
        .download-btn {
            width: 100%;
            padding: 12px 18px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            transition: all 0.2s ease;
            text-decoration: none;
            margin-bottom: 14px;
        }
        
        .download-btn:hover {
            background: #1d4ed8;
        }
        
        .download-btn:active {
            transform: scale(0.98);
        }
        
        .download-btn svg {
            width: 16px;
            height: 16px;
        }
        
        .password-hint {
            text-align: center;
            padding: 8px 10px;
            background: #f9fafb;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }
        
        .password-hint-label {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 2px;
        }
        
        .password-value {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            letter-spacing: 1.5px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="background-animation">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>

    <div class="container download-container">
        <div class="download-card">
            <div class="file-header">
                <div class="file-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/>
                        <polyline points="13 2 13 9 20 9"/>
                    </svg>
                </div>
                <div class="file-info">
                    <div class="file-name"><?php echo htmlspecialchars($fileInfo['original_name']); ?></div>
                    <div class="file-meta"><?php echo strtoupper($extension); ?> Document (<?php echo number_format($fileInfo['size'] / (1024 * 1024), 2); ?> MB)</div>
                </div>
            </div>
            
            <a href="?id=<?php echo urlencode($fileId); ?>&action=download" class="download-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Download File
            </a>
            
            <div class="password-hint">
                <div class="password-hint-label">Password:</div>
                <div class="password-value"><?php echo htmlspecialchars($fileInfo['password']); ?></div>
            </div>
        </div>
    </div>
</body>
</html>