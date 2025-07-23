<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = intval($_POST['id']);
  $conn->query("UPDATE confessions SET likes = likes + 1 WHERE id = $id");

  $res = $conn->query("SELECT likes FROM confessions WHERE id = $id");
  $row = $res->fetch_assoc();
  echo json_encode(['likes' => $row['likes']]);
}
