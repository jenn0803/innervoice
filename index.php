<?php include 'db.php';

function getReactions($conn, $confession_id) {
  $stmt = $conn->prepare("SELECT emoji, COUNT(*) as count FROM reactions WHERE confession_id = ? GROUP BY emoji");
  $stmt->bind_param("i", $confession_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $reactions = [];
  while ($row = $result->fetch_assoc()) {
    $reactions[$row['emoji']] = $row['count'];
  }
  return $reactions;
}

function getReplies($conn, $confession_id) {
  $stmt = $conn->prepare("SELECT message, created_at FROM replies WHERE confession_id = ? ORDER BY created_at ASC");
  $stmt->bind_param("i", $confession_id);
  $stmt->execute();
  return $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>InnerVoice Wall</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

    body {
      background: #fff4fa;
      font-family: 'Poppins', sans-serif;
    }

    .header {
      text-align: center;
      margin-bottom: 30px;
    }

    .wall-container {
      max-width: 700px;
      margin: auto;
      padding: 20px;
      background: #fff0f5;
      border-radius: 20px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }

    .confession {
      background: #ffffff;
      padding: 15px;
      border-radius: 12px;
      margin-bottom: 25px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      text-align: left;
    }

    .reactions {
      margin-top: 10px;
    }

    .emoji-btn {
      border: none;
      background: transparent;
      cursor: pointer;
      font-size: 1.2rem;
      margin-right: 10px;
    }

    .reply-form {
      margin-top: 10px;
      display: flex;
      gap: 10px;
    }

    .reply-form input {
      flex: 1;
      padding: 6px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    .replies {
      margin-top: 10px;
    }

    .reply {
      background: #ffeef5;
      padding: 8px;
      border-radius: 8px;
      margin-top: 4px;
    }

    form {
      margin-top: 40px;
      text-align: center;
    }

    textarea {
      width: 100%;
      height: 100px;
      padding: 10px;
      font-size: 1rem;
      border-radius: 8px;
      border: 1px solid #ccc;
      resize: none;
    }

    button {
      margin-top: 10px;
      background-color: #ec94b8;
      color: white;
      padding: 10px 25px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 1rem;
      transition: background 0.3s;
    }

    button:hover {
      background-color: #d86a9f;
    }

    .logo {
      font-size: 2rem;
      font-weight: bold;
      color: #ec94b8;
    }

    small {
      color: #999;
    }
  </style>
</head>
<body>
  <div class="wall-container">
    <div class="header">
      <div class="logo">💭 InnerVoice</div>
      <p>Feel free to express your thoughts anonymously 🌸</p>
    </div>

    <!-- 🌼 Confessions Display -->
    <?php
    $sql = "SELECT * FROM confessions ORDER BY created_at DESC";
    $result = $conn->query($sql);
    if ($result->num_rows > 0):
      while ($row = $result->fetch_assoc()):
        $reactions = getReactions($conn, $row['id']);
        $replies = getReplies($conn, $row['id']);
    ?>
      <div class="confession" data-id="<?= $row['id'] ?>">
        <p><?= htmlspecialchars($row['message']) ?></p>
        <small><?= $row['created_at'] ?></small>

        <!-- ❤️ Emoji Reaction Buttons -->
        <div class="reactions">
          <?php foreach (['❤️','😂','😢','😮','👏'] as $emoji): ?>
            <button class="emoji-btn" data-emoji="<?= $emoji ?>"><?= $emoji ?> <?= $reactions[$emoji] ?? 0 ?></button>
          <?php endforeach; ?>
        </div>

        <!-- 💬 Reply Form -->
        <form class="reply-form">
          <input type="text" name="reply" placeholder="Reply anonymously..." required />
          <button type="submit">Reply</button>
        </form>

        <!-- 🧵 Replies -->
        <div class="replies">
          <?php while($reply = $replies->fetch_assoc()): ?>
            <div class="reply">
              <?= htmlspecialchars($reply['message']) ?>
              <small><?= $reply['created_at'] ?></small>
            </div>
          <?php endwhile; ?>
        </div>
      </div>
    <?php endwhile; endif; ?>

    <!-- 📝 Confession Form -->
    <form id="confessionForm">
      <h3>Write your confession 💌</h3>
      <textarea name="confession" id="confessionInput" placeholder="Something I never said..." required></textarea>
      <br>
      <button type="submit">Post</button>
    </form>
  </div>

  <!-- JS Scripts -->
  <script>
    document.getElementById('confessionForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const message = document.getElementById('confessionInput').value.trim();

      if (!message || message.length < 5) return alert("Please write something meaningful.");

      // Basic profanity block
      const badWords = ["abuse", "idiot", "hate"]; // add more
      const lowerMsg = message.toLowerCase();
      if (badWords.some(word => lowerMsg.includes(word))) {
        alert("Please avoid inappropriate words.");
        return;
      }

      const formData = new FormData();
      formData.append('confession', message);

      const res = await fetch('confessions/saveconfess.php', {
        method: 'POST',
        body: formData
      });

      if (res.ok) {
        const response = await res.json();
        alert("Confession posted!");
        location.reload();
      } else {
        alert("Failed to post 😞");
      }
    });

    // Emoji Reaction Handler
    document.querySelectorAll('.emoji-btn').forEach(button => {
      button.addEventListener('click', async function () {
        const confessionId = this.closest('.confession').dataset.id;
        const emoji = this.dataset.emoji;

        const res = await fetch('confessions/react.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `id=${confessionId}&emoji=${emoji}`
        });

        if (res.ok) location.reload();
      });
    });

    // Reply Handler
    document.querySelectorAll('.reply-form').forEach(form => {
      form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const confessionId = this.closest('.confession').dataset.id;
        const replyMsg = this.querySelector('input[name=reply]').value.trim();

        if (!replyMsg) return;
        if (replyMsg.length < 2) return alert("Reply is too short!");

        const res = await fetch('confessions/reply.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `id=${confessionId}&reply=${encodeURIComponent(replyMsg)}`
        });

        if (res.ok) location.reload();
      });
    });
  </script>
</body>
</html>
