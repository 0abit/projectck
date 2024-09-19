<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id']) && $_COOKIE['user_id']){
   $tutor_id = $_COOKIE['tutor_id'];
   $user_id = $_COOKIE ['user_id'];
}else{
   $tutor_id = '';
   $user_id = '';
   header('location:login.php');
}

$select_contents = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?");
$select_contents->execute([$tutor_id]);
$total_contents = $select_contents->rowCount();

$select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
$select_playlists->execute([$tutor_id]);
$total_playlists = $select_playlists->rowCount();

// Update the likes query to join with the content table
$select_likes = $conn->prepare("SELECT * FROM `likes` WHERE content_id IN (SELECT id FROM `content` WHERE tutor_id = ?)");
$select_likes->execute([$tutor_id]);
$total_likes = $select_likes->rowCount();

// Update the comments query to join with the content table
$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE content_id IN (SELECT id FROM `content` WHERE tutor_id = ?)");
$select_comments->execute([$tutor_id]);
$total_comments = $select_comments->rowCount();

$select_users = $conn->prepare("SELECT COUNT(*) FROM `users`");
$select_users->execute();
$total_users = $select_users->fetchColumn();

$select_notify = $conn->prepare("SELECT COUNT(*) FROM `notifications`");
$select_notify->execute();
$total_notifications = $select_notify->fetchColumn();

// Fetch total messages from the contact table
$select_messages = $conn->prepare("SELECT COUNT(*) FROM `contact`");
$select_messages->execute();
$total_messages = $select_messages->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="dashboard">

   <h1 class="heading">dashboard</h1>

   <div class="box-container">
      <div class="box">
         <h3><?= $total_contents; ?></h3>
         <p>total contents</p>
         <a href="add_content.php" class="btn">add new content</a>
      </div>

      <div class="box">
         <h3><?= $total_playlists; ?></h3>
         <p>total playlists</p>
         <a href="add_playlist.php" class="btn">add new playlist</a>
      </div>

      <div class="box">
         <h3><?= $total_comments; ?></h3>
         <p>total comments</p>
         <a href="comments.php" class="btn">view comments</a>
      </div>

      <div class="box">
         <h3><?= $total_users; ?></h3>
         <p>total users</p>
         <a href="usermanage.php" class="btn">view users</a>
      </div>

      <div class="box">
         <h3><?= $total_messages; ?></h3>
         <p>total messages</p>
         <a href="view_contact.php" class="btn">view messages</a>
      </div>

      <div class="box">
         <h3><?= $total_notifications; ?></h3>
         <p>total notifications</p>
         <a href="notifications.php" class="btn">view notifications</a>
      </div>

   </div>

</section>

<script src="../js/admin_script.js"></script>

</body>
</html>