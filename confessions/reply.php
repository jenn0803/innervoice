<?php
include '../db.php';
$id = $_POST['id'] ?? null;
$reply = trim($_POST['reply'] ?? '');
if ($id && $reply !== '') {
  $stmt = $conn->prepare("INSERT INTO replies (confession_id, message) VALUES (?, ?)");
  $stmt->bind_param("is", $id, $reply);
  $stmt->execute();
}
?>
