<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Confessions</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="container">
    <h2>Anonymous Confessions</h2>
    <?php
    $sql = "SELECT * FROM confessions ORDER BY created_at DESC";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()): ?>
      <div class="confession">
        <p><?= htmlspecialchars($row['message']) ?></p>
        <small><?= $row['created_at'] ?></small>
      </div>
    <?php endwhile; ?>
    <a href="index.php" class="btn">Back Home</a>
  </div>
</body>
</html>


<!-- CREATE DATABASE IF NOT EXISTS innervoice;
USE innervoice;

CREATE TABLE confessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  message TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
); -->