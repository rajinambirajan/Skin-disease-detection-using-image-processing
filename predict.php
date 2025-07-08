<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}

// Save uploaded file
$target_dir = "uploads/";
if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

$target_file = $target_dir . basename($_FILES["image"]["name"]);
move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

// Run prediction
$escaped_path = escapeshellarg($target_file);
$command = escapeshellcmd("python predict.py $escaped_path");
$output = shell_exec($command);

list($predicted, $confidence) = explode('|', trim($output));

// Disease info
$disease_info = [
    'acne' => 'Acne is a common skin condition. Treatment includes cleansers, topical creams, and in some cases antibiotics.',
    'eczema' => 'Eczema causes itchy, dry skin. Treatment includes moisturizers, steroid creams, and avoiding triggers.',
    'Psoriasis' => 'Psoriasis leads to scaly skin patches. Treatments include creams, light therapy, and medications.'
];
$info = $disease_info[$predicted] ?? "No information available.";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prediction Result</title>
    <style>
        body {
            background:rgb(182, 184, 225);
            font-family: 'Segoe UI', sans-serif;
            text-align: center;
            padding-top: 60px;
        }

        .result-box {
            background: white;
            max-width: 600px;
            margin: auto;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        h2 {
            color: #1f2937;
        }

        img {
            width: 300px;
            border-radius: 12px;
            margin-bottom: 1rem;
            border: 2px solid #ddd;
        }

        .prediction {
            font-size: 1.6rem;
            color: #2563eb;
            font-weight: bold;
        }

        .confidence {
            color: #059669;
            font-size: 1rem;
            margin-top: 10px;
        }

        .info {
            margin-top: 1rem;
            color: #374151;
            font-size: 1rem;
            line-height: 1.5;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #2563eb;
            font-weight: 600;
        }

        /* Google Translate style override */
        #google_translate_element {
            position: fixed;
            top: 10px;
            right: 20px;
            z-index: 999;
        }
    </style>
</head>
<body>
    <!-- Google Translate Dropdown -->
    <div id="google_translate_element"></div>
    <script type="text/javascript">
      function googleTranslateElementInit() {
        new google.translate.TranslateElement(
            {pageLanguage: 'en'},
            'google_translate_element'
        );
      }
    </script>
    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <!-- Result box -->
    <div class="result-box">
        <h2>Prediction Result</h2>
        <img src="<?php echo $target_file; ?>" alt="Uploaded Image">
        <p class="prediction">Disease: <?php echo ucfirst($predicted); ?></p>
        <p class="confidence">Confidence: <?php echo round($confidence, 2); ?>%</p>
        <p class="info"><?php echo $info; ?></p>
        <a href="index.php">🔙 Back</a>
    </div>
</body>
</html>
