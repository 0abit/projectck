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
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = unique_id().'.'.$ext;
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_files/'.$rename;

   $check_email = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $check_email->execute([$email]);

   if($check_email->rowCount() > 0){
      $display_messages[] = 'Email already exists!';
   }else{
      if($image_size > 2000000){
         $display_messages[] = 'Image size is too large!';
      }else{
         move_uploaded_file($image_tmp_name, $image_folder);

         // Generate a unique ID for the new student
         $student_id = generate_unique_id();

         $insert_student = $conn->prepare("INSERT INTO `users`(id, name, email, password, image) VALUES(?,?,?,?,?)");
         $insert_student->execute([$student_id, $name, $email, $password, $rename]);
         
         $display_messages[] = "New student added successfully! ";
      }
   }
}

function generate_unique_id() {
    global $conn;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    do {
        $id = '';
        for ($i = 0; $i < 20; $i++) {
            $id .= $characters[rand(0, strlen($characters) - 1)];
        }
        $check_id = $conn->prepare("SELECT id FROM `users` WHERE id = ?");
        $check_id->execute([$id]);
    } while ($check_id->rowCount() > 0);
    return $id;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Add Student</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<?php
if(!empty($display_messages)){
   foreach($display_messages as $msg){
      echo '<div class="message">
         <span>'.$msg.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>';
   }
}
?>

<section class="form-container">
   <form action="" method="post" enctype="multipart/form-data">
      <h3>Add New Student</h3>
      <p>Name <span>*</span></p>
      <input type="text" name="name" placeholder="Enter student name" maxlength="50" required class="box">
      <p>Email <span>*</span></p>
      <input type="email" name="email" placeholder="Enter student email" maxlength="50" required class="box">
      <p>Password <span>*</span></p>
      <input type="password" name="password" placeholder="Enter student password" maxlength="20" required class="box">
      <p>Profile Image <span>*</span></p>
      <input type="file" name="image" accept="image/*" required class="box">
      <input type="submit" name="submit" value="Add Student" class="btn">
      <a href="usermanage.php" class="option-btn">Go Back</a>
   </form>
</section>



<script src="../js/admin_script.js"></script>

</body>
</html>

