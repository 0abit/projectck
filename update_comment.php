<?php
include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
   exit();
}

if(isset($_POST['update_comment']) && isset($_POST['comment_id']) && isset($_POST['update_box'])){
   $update_id = $_POST['comment_id'];
   $update_id = filter_var($update_id, FILTER_SANITIZE_STRING);
   $update_box = $_POST['update_box'];
   $update_box = filter_var($update_box, FILTER_SANITIZE_STRING);

   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ? AND user_id = ?");
   $verify_comment->execute([$update_id, $user_id]);

   if($verify_comment->rowCount() > 0){
      $update_comment = $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ?");
      $update_comment->execute([$update_box, $update_id]);
      echo json_encode(['status' => 'success', 'message' => 'Comment updated successfully']);
   }else{
      echo json_encode(['status' => 'error', 'message' => 'Comment not found or you are not authorized to edit this comment']);
   }
}else{
   echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
