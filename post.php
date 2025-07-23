<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Post Confession</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="container">
    <h2>Speak your mind anonymously</h2>
    <form action="confessions/saveconfess.php" method="POST">
      <textarea name="confession" placeholder="Type your thoughts here..." required></textarea>
      <button type="submit">Post</button>
    </form>
    <a href="index.php" class="btn">Back Home</a>
  </div>
</body>
</html>