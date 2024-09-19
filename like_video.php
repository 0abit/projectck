<?php
include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
   exit();
}

if(isset($_POST['content_id'])){
   $content_id = $_POST['content_id'];
   $content_id = filter_var($content_id, FILTER_SANITIZE_STRING);

   $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
   $select_content->execute([$content_id]);
   $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);

   if($fetch_content){
      $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND content_id = ?");
      $select_likes->execute([$user_id, $content_id]);

      if($select_likes->rowCount() > 0){
         $remove_likes = $conn->prepare("DELETE FROM `likes` WHERE user_id = ? AND content_id = ?");
         $remove_likes->execute([$user_id, $content_id]);
         $liked = false;
      }else{
         $insert_likes = $conn->prepare("INSERT INTO `likes`(user_id, content_id) VALUES(?,?)");
         $insert_likes->execute([$user_id, $content_id]);
         $liked = true;
      }

      $total_likes = $conn->prepare("SELECT COUNT(*) FROM `likes` WHERE content_id = ?");
      $total_likes->execute([$content_id]);
      $total_likes = $total_likes->fetchColumn();

      echo json_encode(['status' => 'success', 'liked' => $liked, 'total_likes' => $total_likes]);
   }else{
      echo json_encode(['status' => 'error', 'message' => 'Content not found']);
   }
}else{
   echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
