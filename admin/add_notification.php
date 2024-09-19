<?php
include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}

$display_messages = [];

if(isset($_POST['submit'])){
   $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
   $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

   $insert_notification = $conn->prepare("INSERT INTO `notifications`(title, message) VALUES(?,?)");
   $insert_notification->execute([$title, $message]);
   
   $display_messages[] = "New notification added successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Add Notification</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>



<section class="form-container">
   <form action="" method="post">
      <h3>Add New Notification</h3>
      <p>Title <span>*</span></p>
      <input type="text" name="title" placeholder="Enter notification title" maxlength="255" required class="box">
      <p>Message <span>*</span></p>
      <textarea name="message" placeholder="Enter notification message" maxlength="1000" required class="box"></textarea>
      <input type="submit" name="submit" value="Add Notification" class="btn">
      <a href="notifications.php" class="option-btn">Go Back</a>
   </form>
</section>


<script src="../js/admin_script.js"></script>

</body>
</html>
