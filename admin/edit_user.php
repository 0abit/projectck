<?php
include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}

if(isset($_GET['id'])){
   $user_id = $_GET['id'];
}else{
   $user_id = '';
   header('location:usermanage.php');
}

if(isset($_POST['submit'])){
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);

   $update_user = $conn->prepare("UPDATE `users` SET name = ?, email = ? WHERE id = ?");
   $update_user->execute([$name, $email, $user_id]);

   $message[] = 'User updated successfully!';
}

$select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$select_user->execute([$user_id]);
$fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Edit User</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="form-container">
   <form action="" method="post" enctype="multipart/form-data">
      <h3>Edit User</h3>
      <p>Name <span>*</span></p>
      <input type="text" name="name" placeholder="Enter user name" maxlength="50" required class="box" value="<?= $fetch_user['name']; ?>">
      <p>Email <span>*</span></p>
      <input type="email" name="email" placeholder="Enter user email" maxlength="50" required class="box" value="<?= $fetch_user['email']; ?>">
      <input type="submit" name="submit" value="Update User" class="btn">
      <a href="usermanage.php" class="option-btn">Go Back</a>
   </form>
</section>



<script src="../js/admin_script.js"></script>

</body>
</html>
