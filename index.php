<?php
session_start();
require_once 'config.php';

// Process POST submissions using the PRG pattern.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['generate'])) {
        // Process Generate A Key submission.
        $text = isset($_POST['text']) ? trim($_POST['text']) : '';
        if (empty($text)) {
            $_SESSION['alert_msg'] = 'Please enter text to copy.';
            $_SESSION['alert_type'] = 'error';
        } else {
            $user_ip = $_SERVER['REMOTE_ADDR'];
            // Generate a unique 8-digit key.
            do {
                $unique_key = strval(random_int(10000000, 99999999));
                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM copies WHERE unique_key = ?");
                $stmt->execute([$unique_key]);
                $row = $stmt->fetch();
            } while ($row && $row['count'] > 0);

            // Insert the new record.
            $stmt = $pdo->prepare("INSERT INTO copies (unique_key, text, user_ip, created_at, status) VALUES (?, ?, ?, NOW(), 0)");
            if ($stmt->execute([$unique_key, $text, $user_ip])) {
                $_SESSION['alert_msg'] = "Your key: {$unique_key}. Use it to retrieve your text.";
                $_SESSION['alert_type'] = 'success';
                $_SESSION['generated_key'] = $unique_key;
            } else {
                $_SESSION['alert_msg'] = "Error occurred while generating key. Please try again.";
                $_SESSION['alert_type'] = 'error';
            }
        }
        $_SESSION['activeTab'] = 'generate';
    } elseif (isset($_POST['get'])) {
        // Process Get My Text submission.
        $key = isset($_POST['key']) ? trim($_POST['key']) : '';
        if (empty($key)) {
            $_SESSION['alert_msg'] = 'Please enter a key.';
            $_SESSION['alert_type'] = 'error';
        } else {
            $stmt = $pdo->prepare("SELECT * FROM copies WHERE unique_key = ?");
            $stmt->execute([$key]);
            $record = $stmt->fetch();
            if (!$record) {
                $_SESSION['alert_msg'] = 'Key not found!';
                $_SESSION['alert_type'] = 'error';
            } elseif ($record['status'] == 1) {
                $_SESSION['alert_msg'] = 'Key already used.';
                $_SESSION['alert_type'] = 'error';
            } else {
                // Mark key as used and set display text.
                $used_at = date("Y-m-d H:i:s");
                $user_ip = $_SERVER['REMOTE_ADDR'];
                $updateStmt = $pdo->prepare("UPDATE copies SET status = 1, used_at = ?, used_by_ip = ? WHERE unique_key = ?");
                $updateStmt->execute([$used_at, $user_ip, $key]);
                $_SESSION['display_text'] = htmlspecialchars($record['text']);
            }
        }
        $_SESSION['activeTab'] = 'get';
    }
    header("Location: index.php");
    exit();
}

// Determine which tab should be active.
if (isset($_GET['key'])) {
    $activeTab = 'get';
    $key = trim($_GET['key']);
    if (!empty($key)) {
        $stmt = $pdo->prepare("SELECT * FROM copies WHERE unique_key = ?");
        $stmt->execute([$key]);
        $record = $stmt->fetch();
        if (!$record) {
            $alertMsg = 'Key not found!';
            $alertType = 'error';
        } elseif ($record['status'] == 1) {
            $alertMsg = 'Key already used.';
            $alertType = 'error';
        } else {
            // Mark key as used and prepare to display text.
            $used_at = date("Y-m-d H:i:s");
            $user_ip = $_SERVER['REMOTE_ADDR'];
            $updateStmt = $pdo->prepare("UPDATE copies SET status = 1, used_at = ?, used_by_ip = ? WHERE unique_key = ?");
            $updateStmt->execute([$used_at, $user_ip, $key]);
            $displayText = htmlspecialchars($record['text']);
        }
    } else {
        $alertMsg = 'Please provide a valid key.';
        $alertType = 'error';
    }
} elseif (isset($_SESSION['activeTab'])) {
    $activeTab = $_SESSION['activeTab'];
} else {
    $activeTab = 'generate';
}

// Retrieve one-time session data.
$alertMsg      = $_SESSION['alert_msg']     ?? $alertMsg ?? '';
$alertType     = $_SESSION['alert_type']    ?? $alertType ?? '';
$generated_key = $_SESSION['generated_key'] ?? '';
$displayText   = $_SESSION['display_text']  ?? $displayText ?? '';

unset($_SESSION['alert_msg'], $_SESSION['alert_type'], $_SESSION['generated_key'], $_SESSION['display_text'], $_SESSION['activeTab']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MyCopyPaste - Secure Text Sharing Across Devices</title>
  
  <!-- Primary Meta Tags -->
  <meta name="title" content="MyCopyPaste - Secure Text Sharing Across Devices">
  <meta name="description" content="Share text securely across any device with MyCopyPaste. Generate unique keys, scan QR codes, and transfer text instantly. Fast, secure, and mobile-friendly.">
  <meta name="keywords" content="text sharing, copy paste, secure sharing, QR code, mobile friendly, cross-device sharing">
  <meta name="author" content="MyCopyPaste">
  <meta name="robots" content="index, follow">
  
  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://mycopypaste.io/">
  <meta property="og:title" content="MyCopyPaste - Secure Text Sharing Across Devices">
  <meta property="og:description" content="Share text securely across any device with MyCopyPaste. Generate unique keys, scan QR codes, and transfer text instantly.">
  <meta property="og:image" content="https://mycopypaste.io/preview.jpg">
  
  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="https://mycopypaste.io/">
  <meta property="twitter:title" content="MyCopyPaste - Secure Text Sharing Across Devices">
  <meta property="twitter:description" content="Share text securely across any device with MyCopyPaste. Generate unique keys, scan QR codes, and transfer text instantly.">
  <meta property="twitter:image" content="https://mycopypaste.io/preview.jpg">
  
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="favicon.png">
  <link rel="apple-touch-icon" href="favicon.png">
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Styles -->
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  
  <!-- Theme Color for Mobile Browsers -->
  <meta name="theme-color" content="#2563eb">
</head>
<body>
  <header>
    <h1>MyCopyPaste</h1>
    <p class="tagline">Effortless Text Sharing, Anywhere, Anytime</p>
    <p class="description">
      MyCopyPaste is designed to provide an unmatched experience in device‑to‑device copy and paste.
      Enjoy quick, reliable, and secure text transfers that enhance your productivity wherever you are in the world.
    </p>
  </header>
  
  <div class="container">
    <?php if (!empty($alertMsg)): ?>
      <div class="alert <?php echo $alertType; ?>">
        <?php echo $alertMsg; ?>
      </div>
    <?php endif; ?>

    <!-- Tab Navigation -->
    <div class="tab-nav">
      <button id="btnGenerate" class="tab-btn <?php echo ($activeTab == 'generate') ? 'active' : ''; ?>">Generate A Key</button>
      <button id="btnGet" class="tab-btn <?php echo ($activeTab == 'get') ? 'active' : ''; ?>">Get My Text</button>
    </div>

    <!-- Single card containing both tab contents -->
    <div class="card">
      <!-- Generate A Key Section -->
      <div id="generateForm" class="tab-content" style="display: <?php echo ($activeTab == 'generate') ? 'block' : 'none'; ?>;">
        <?php if (empty($generated_key)): ?>
          <form action="" method="post">
            <div class="form-group">
              <label for="text">Your Text</label>
              <textarea id="text" name="text" placeholder="Enter your text here..." required></textarea>
            </div>
            <div class="form-group">
              <button type="submit" name="generate" class="btn primary-btn">Generate Key</button>
            </div>
          </form>
        <?php else: ?>
          <div class="generated-info">
            <p><strong>Your Key: </strong><?php echo $generated_key; ?></p>
            <?php
              // Use the site's root URL.
              $domain = "https://mycopypaste.io/";
              $link = $domain . "?key=" . $generated_key;
            ?>
            <p><strong>Scan the QR Code or use the link below:</strong></p>
            <div class="qr-container">
              <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode(trim($link)); ?>" alt="QR Code">
            </div>
            <div class="link-container">
              <input type="text" value="<?php echo $link; ?>" id="copyLink" readonly>
              <button class="btn copy-link-btn" onclick="copyLinkText()">Copy Link</button>
            </div>
          </div>
        <?php endif; ?>
      </div>


      <!-- Get My Text Section -->
      <div id="getForm" class="tab-content" style="display: <?php echo ($activeTab == 'get') ? 'block' : 'none'; ?>;">
         <?php if (empty($displayText)): ?>
         <form action="" method="post">
           <div class="form-group">
             <label for="key">Your Unique Key</label>
             <input type="text" id="key" name="key" placeholder="8-digit key" required pattern="\d{8}" title="Please enter an 8-digit key">
           </div>
           <div class="form-group">
             <button type="submit" name="get" class="btn secondary-btn">Get My Text</button>
           </div>
         </form>
         <?php else: ?>
         <div class="result-text">
           <div class="text-header">
             <h2><i class="fas fa-file-alt"></i> Your Copied Text</h2>
             <div class="text-actions">
               <button class="action-btn" onclick="copyText()" title="Copy to Clipboard">
                 <i class="fas fa-copy"></i>
                 <span>Copy</span>
               </button>
               <button class="action-btn" onclick="downloadText()" title="Download as Text File">
                 <i class="fas fa-download"></i>
                 <span>Download</span>
               </button>
             </div>
           </div>
           <div class="text-container">
             <textarea class="text-display" id="textToCopy" readonly><?php echo htmlspecialchars(trim($displayText), ENT_QUOTES, 'UTF-8'); ?></textarea>
             <div class="text-info">
               <span class="text-length"><?php echo strlen($displayText); ?> characters</span>
               <span class="text-lines"><?php echo substr_count($displayText, "\n") + 1; ?> lines</span>
             </div>
           </div>
         </div>
         <?php endif; ?>
      </div>
    </div>
    
    <!-- FAQs Section placed above the footer -->
    <section class="faq-section">
      <h3>Frequently Asked Questions</h3>
      <div class="faq">
        <div class="faq-item">
          <div class="faq-question">How do I generate a key? <span class="faq-toggle">+</span></div>
          <div class="faq-answer">Enter your text in the "Generate A Key" section and click the button to receive your unique key.</div>
        </div>
        <div class="faq-item">
          <div class="faq-question">What happens when I use a key? <span class="faq-toggle">+</span></div>
          <div class="faq-answer">When you retrieve your text using a key, the saved text is displayed and the key is marked as used.</div>
        </div>
        <div class="faq-item">
          <div class="faq-question">Is my data secure? <span class="faq-toggle">+</span></div>
          <div class="faq-answer">Yes, we use secure PHP methods and PDO prepared statements to protect your data.</div>
        </div>
      </div>
    </section>
  </div>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> MyCopyPaste. All rights reserved.</p>
  </footer>

  <script src="js/custom.js"></script>
</body>
</html>