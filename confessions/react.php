<?php
include '../db.php';
$id = $_POST['id'] ?? null;
$emoji = $_POST['emoji'] ?? '';
if ($id && $emoji) {
  $stmt = $conn->prepare("INSERT INTO reactions (confession_id, emoji) VALUES (?, ?)");
  $stmt->bind_param("is", $id, $emoji);
  $stmt->execute();
}
?>
