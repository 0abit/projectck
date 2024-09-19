<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}

// Handle notification deletion
if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_notification = $conn->prepare("DELETE FROM `notifications` WHERE id = ?");
   $delete_notification->execute([$delete_id]);
   header('location:notifications.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Notifications</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

   <style>
      .notification-table{
         width: 100%;
         border-collapse: collapse;
}
.notification-table td, .user-table td {
         padding: 10px;
         text-align: left;
         border-bottom: 1px solid #ddd;
         font-size: 2rem; 
         font-weight: bold;
}

.notification-table th, .user-table th {
         padding: 10px;
         text-align: left;
         border-bottom: 1px solid #ddd;
         background-color: #f2f2f2;
         font-size: 2rem; 
         font-weight: bold; 
      }

   </style>

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="notifications">

   <h1 class="heading">Notifications</h1>

   <div class="box-container">

      <div class="box" style="text-align: center;">

         <a href="add_notification.php" class="btn">Add New Notification</a>
      </div>

      <div class="box">
         <div class="flex-btn" style="margin-bottom: 1rem;">
            <a href="?sort=title" class="option-btn">Sort by Title</a>
            <a href="?sort=created_at" class="option-btn">Sort by Date</a>
         </div>
         <table class="notification-table">
            <thead>
               <tr>
                  <th>ID</th>
                  <th>Title</th>
                  <th>Message</th>
                  <th>Actions</th>
               </tr>
            </thead>
            <tbody>
               <?php
                  $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
                  $allowed_sorts = ['id', 'title', 'created_at'];
                  $sort = in_array($sort, $allowed_sorts) ? $sort : 'id';

                  $select_notifications = $conn->prepare("SELECT * FROM `notifications` ORDER BY $sort");
                  $select_notifications->execute();
                  if($select_notifications->rowCount() > 0){
                     while($fetch_notification = $select_notifications->fetch(PDO::FETCH_ASSOC)){
                        $truncated_message = strlen($fetch_notification['message']) > 50 ? substr($fetch_notification['message'], 0, 50) . '...' : $fetch_notification['message'];
               ?>
               <tr>
                  <td><?= htmlspecialchars($fetch_notification['id']); ?></td>
                  <td><?= htmlspecialchars($fetch_notification['title']); ?></td>
                  <td><?= htmlspecialchars($truncated_message); ?></td>
                  <td>
                     <a href="edit_notification.php?id=<?= htmlspecialchars($fetch_notification['id']); ?>" class="inline-btn">Edit</a>
                     <a href="notifications.php?delete=<?= htmlspecialchars($fetch_notification['id']); ?>" class="inline-delete-btn" onclick="return confirm('Delete this notification?');">Delete</a>
                  </td>
               </tr>
               <?php
                     }
                  } else {
                     echo '<tr><td colspan="4">No notifications found</td></tr>';
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