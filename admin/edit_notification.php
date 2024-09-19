<?php
include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}

$display_messages = [];

if(isset($_GET['id'])){
   $edit_id = $_GET['id'];
   $select_notification = $conn->prepare("SELECT * FROM `notifications` WHERE id = ?");
   $select_notification->execute([$edit_id]);
   if($select_notification->rowCount() > 0){
      $fetch_notification = $select_notification->fetch(PDO::FETCH_ASSOC);
   }else{
      header('location:notifications.php');
   }
}else{
   header('location:notifications.php');
}

if(isset($_POST['submit'])){
   $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
   $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

   $update_notification = $conn->prepare("UPDATE `notifications` SET title = ?, message = ? WHERE id = ?");
   $update_notification->execute([$title, $message, $edit_id]);
   
   $display_messages[] = "Notification updated successfully!";
}

// Fetch the latest notification
$latest_notification = $conn->prepare("SELECT * FROM `notifications` ORDER BY created_at DESC LIMIT 1");
$latest_notification->execute();
$latest_notification = $latest_notification->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Edit Notification</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>



<!-- Display the latest notification above the form -->


<section class="form-container">
   <form action="" method="post">
      <h3>Edit Notification</h3>
      <p>Title <span>*</span></p>
      <input type="text" name="title" value="<?= $fetch_notification['title']; ?>" placeholder="Enter notification title" maxlength="255" required class="box">
      <p>Message <span>*</span></p>
      <textarea name="message" placeholder="Enter notification message" maxlength="1000" required class="box"><?= $fetch_notification['message']; ?></textarea>
      <input type="submit" name="submit" value="Update Notification" class="btn">
      <a href="notifications.php" class="option-btn">Go Back</a>
   </form>
</section>



<script src="../js/admin_script.js"></script>

</body>
</html>