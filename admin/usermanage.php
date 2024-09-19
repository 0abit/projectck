<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}

// Handle user deletion
if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_user = $conn->prepare("DELETE FROM `users` WHERE id = ?");
   $delete_user->execute([$delete_id]);
   header('location:usermanage.php');
}

// Handle student addition
if(isset($_POST['add_student'])){
   $name = $_POST['name'];
   $email = $_POST['email'];
   $password = sha1($_POST['password']);

   // Check if email already exists
   $check_email = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $check_email->execute([$email]);

   if($check_email->rowCount() > 0){
      $message[] = 'Email already exists!';
   }else{
      $insert_student = $conn->prepare("INSERT INTO `users`(name, email, password) VALUES(?,?,?)");
      $insert_student->execute([$name, $email, $password]);
      $message[] = 'New student added successfully!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>User Management</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

   <style>
      .user-table td {
         font-size: 1.8rem; /* Match this with your button font size */
      }
      .user-table th {
         font-size: 2rem; /* Increased size for table headers */
         font-weight: bold;
      }
   </style>

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="user-accounts">

   <h1 class="heading">User Accounts</h1>

   <div class="box-container">

      <div class="box" style="text-align: center;">
         <a href="add_student.php" class="btn">Add New Student</a>
      </div>

      <div class="box">
         <div class="flex-btn" style="margin-bottom: 1rem;">
            <a href="?sort=name" class="option-btn">Sort by Name</a>
            <a href="?sort=email" class="option-btn">Sort by Email</a>
         </div>
         <table class="user-table">
            <thead>
               <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Actions</th>
               </tr>
            </thead>
            <tbody>
               <?php
                  $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
                  $allowed_sorts = ['id', 'name', 'email'];
                  $sort = in_array($sort, $allowed_sorts) ? $sort : 'id';

                  $select_users = $conn->prepare("SELECT * FROM `users` ORDER BY $sort");
                  $select_users->execute();
                  if($select_users->rowCount() > 0){
                     while($fetch_user = $select_users->fetch(PDO::FETCH_ASSOC)){
               ?>
               <tr>
                  <td><?= $fetch_user['id']; ?></td>
                  <td><?= $fetch_user['name']; ?></td>
                  <td><?= $fetch_user['email']; ?></td>
                  <td>
                     <a href="edit_user.php?id=<?= $fetch_user['id']; ?>" class="inline-btn">Edit</a>
                     <a href="usermanage.php?delete=<?= $fetch_user['id']; ?>" class="inline-delete-btn" onclick="return confirm('Delete this user?');">Delete</a>
                  </td>
               </tr>
               <?php
                     }
                  } else {
                     echo '<tr><td colspan="4">No users found</td></tr>';
                  }
               ?>
            </tbody>
         </table>
      </div>

   </div>

</section>



<script src="../js/admin_script.js"></script>

</body>
</html>