<?php
session_start();

// Get success data from session
$downloadLink = '';
$password = '';
$showSuccess = false;

if (isset($_GET['success']) && $_GET['success'] == '1') {
    if (isset($_SESSION['download_link']) && isset($_SESSION['password'])) {
        $downloadLink = $_SESSION['download_link'];
        $password = $_SESSION['password'];
        $showSuccess = true;
        
        // Clear session data after retrieving
        unset($_SESSION['download_link']);
        unset($_SESSION['password']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Share - Upload & Share</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="background-animation">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>

    <div class="container">
        <div class="upload-box">
            <div class="icon-wrapper">
                <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                </svg>
            </div>
            
            <h1 class="title">Upload & Share Files</h1>
            <p class="subtitle">Share files securely with anyone using a simple link</p>

            <form action="upload.php" method="POST" enctype="multipart/form-data" id="uploadForm">
                <div class="file-input-wrapper">
                    <input type="file" name="file" id="fileInput" required>
                    <label for="fileInput" class="file-label">
                        <span class="file-label-text">Choose File</span>
                        <span class="file-name">No file chosen</span>
                    </label>
                </div>

                <div class="password-input-wrapper">
                    <label for="customPassword" class="password-input-label">
                        🔒 Set Download Password
                    </label>
                    <input 
                        type="text" 
                        name="custom_password" 
                        id="customPassword" 
                        class="password-input-field"
                        placeholder="Enter custom password"
                        required
                        maxlength="20"
                    >
                    <small class="password-hint">Enter a password (max 20 characters)</small>
                </div>

                <div class="page-select-wrapper">
                    <label for="pageSelect" class="page-select-label">
                        📄 Choose Upload Page
                    </label>
                    <select name="page" id="pageSelect" class="page-select-field" required>
                        <option value="">Select a page...</option>
                        <option value="page1">Page 1</option>
                        <option value="page2">Page 2</option>
                        <option value="page3">Page 3</option>
                        <option value="page4">Page 4</option>
                        <option value="page5">Page 5</option>
                    </select>
                    <small class="password-hint">File will be available on selected page</small>
                </div>

                <button type="submit" class="upload-btn">
                    <span class="btn-text">Upload File</span>
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </button>
            </form>

            <div class="supported-formats">
                <p>Supported formats: ZIP, RAR, TXT, DOC, DOCX, PDF, JPG, PNG, and more</p>
            </div>
        </div>

        <?php if($showSuccess): ?>
        <div class="success-box">
            <div class="success-icon">✓</div>
            <h2>File Uploaded Successfully!</h2>
            <p class="success-message">Share this link with anyone to download your file</p>
            
            <div class="link-box">
                <input type="text" id="shareLink" value="<?php echo htmlspecialchars($downloadLink); ?>" readonly>
                <button onclick="copyLink()" class="copy-btn">Copy</button>
            </div>
            
            <div class="password-display">
                <span class="password-label">Download Password:</span>
                <span class="password-value"><?php echo htmlspecialchars($password); ?></span>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        const fileInput = document.getElementById('fileInput');
        const fileName = document.querySelector('.file-name');
        
        fileInput.addEventListener('change', function() {
            if(this.files && this.files[0]) {
                fileName.textContent = this.files[0].name;
                fileName.style.color = '#fff';
            }
        });

        function copyLink() {
            const linkInput = document.getElementById('shareLink');
            linkInput.select();
            document.execCommand('copy');
            
            const btn = event.target;
            btn.textContent = 'Copied!';
            btn.style.background = '#10b981';
            
            setTimeout(() => {
                btn.textContent = 'Copy';
                btn.style.background = '';
            }, 2000);
        }

        const form = document.getElementById('uploadForm');
        form.addEventListener('submit', function() {
            const btn = form.querySelector('.upload-btn');
            btn.disabled = true;
            btn.querySelector('.btn-text').textContent = 'Uploading...';
        });
    </script>
</body>
</html>