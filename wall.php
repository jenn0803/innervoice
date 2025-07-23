<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>InnerVoice Wall</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    /* Additional aesthetic styles */
    .header {
      font-family: 'Poppins', sans-serif;
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
      margin-bottom: 15px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      text-align: left;
    }

    form {
      margin-top: 30px;
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

    small {
      color: #999;
    }

    .logo {
      font-size: 2rem;
      font-weight: bold;
      color: #ec94b8;
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
    while ($row = $result->fetch_assoc()): ?>
      <div class="confession">
        <p><?= htmlspecialchars($row['message']) ?></p>
        <small><?= $row['created_at'] ?></small>
      </div>
    <?php endwhile; ?>

    <!-- 📝 Confession Form -->
    <form id="confessionForm">
  <h3>Write your confession 💌</h3>
  <textarea name="confession" id="confessionInput" placeholder="Something I never said..." required></textarea>
  <br>
  <button type="submit">Post</button>
</form>
<script>
document.getElementById('confessionForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const message = document.getElementById('confessionInput').value.trim();
  if (!message) return;

  const formData = new FormData();
  formData.append('confession', message);

  const res = await fetch('confessions/saveconfess.php', {
    method: 'POST',
    body: formData
  });

  if (res.ok) {
    const response = await res.json();
    const div = document.createElement('div');
    div.className = 'confession';
    div.innerHTML = `<p>${response.message}</p><small>${response.time}</small>`;
    document.querySelector('.header').after(div);
    document.getElementById('confessionInput').value = "";
  } else {
    alert("Failed to post 😞");
  }
});
</script>

  </div>
</body>
</html>
