<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $target_dir = "uploads/";

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_path = $target_dir . basename($_FILES["image"]["name"]);

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
        // Run Python script
        $command = escapeshellcmd("python predict.py " . escapeshellarg($image_path));
        $output = shell_exec($command);

        echo "<h2>Prediction Result:</h2>";
        echo "<p><strong>Disease Detected:</strong> " . htmlspecialchars($output) . "</p>";
    } else {
        echo "Failed to upload image.";
    }
}
?>
