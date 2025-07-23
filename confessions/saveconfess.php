<?php
include '../db.php';

function containsAbuse($text) {
  $banned = ['abuse1', 'badword2', 'hate', 'kill'];
  $text = strtolower($text);
  foreach ($banned as $word) {
    if (strpos($text, $word) !== false) return true;
  }
  return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $confession = trim($_POST['confession']);
  if (!empty($confession) && !containsAbuse($confession)) {
    $stmt = $conn->prepare("INSERT INTO confessions (message, created_at) VALUES (?, NOW())");
    $stmt->bind_param("s", $confession);
    $stmt->execute();
    $stmt->close();

    echo json_encode([
      "message" => htmlspecialchars($confession),
      "time" => date("Y-m-d H:i:s")
    ]);
    exit();
  }
}

http_response_code(400);
echo json_encode(["error" => "Invalid or offensive content"]);
?>
