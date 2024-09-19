<?php
include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
   exit();
}

if(isset($_POST['content_id']) && isset($_POST['playlist_id'])){
   $content_id = $_POST['content_id'];
   $playlist_id = $_POST['playlist_id'];
   $content_id = filter_var($content_id, FILTER_SANITIZE_STRING);
   $playlist_id = filter_var($playlist_id, FILTER_SANITIZE_STRING);

   $insert_progress = $conn->prepare("INSERT INTO `video_progress`(user_id, content_id, completed) VALUES(?,?,1) ON DUPLICATE KEY UPDATE completed = VALUES(completed)");
   $insert_progress->execute([$user_id, $content_id]);

   $total_videos = $conn->prepare("SELECT COUNT(*) FROM `content` WHERE playlist_id = ?");
   $total_videos->execute([$playlist_id]);
   $total_videos = $total_videos->fetchColumn();

   $completed_videos = $conn->prepare("SELECT COUNT(*) FROM `video_progress` WHERE user_id = ? AND content_id IN (SELECT id FROM `content` WHERE playlist_id = ?) AND completed = 1");
   $completed_videos->execute([$user_id, $playlist_id]);
   $completed_videos = $completed_videos->fetchColumn();

   $progress = min(($completed_videos / $total_videos) * 100, 100);

   echo json_encode(['status' => 'success', 'progress' => $progress]);
}else{
   echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>