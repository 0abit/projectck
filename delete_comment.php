<?php

include 'components/connect.php';

if(isset($_POST['comment_id'])){
    $comment_id = $_POST['comment_id'];
    $comment_id = filter_var($comment_id, FILTER_SANITIZE_STRING);

    if(isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        echo json_encode(['status' => 'error']);
        exit();
    }

    $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ? AND user_id = ?");
    $verify_comment->execute([$comment_id, $user_id]);

    if($verify_comment->rowCount() > 0){
        $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
        $delete_comment->execute([$comment_id]);
        echo json_encode(['status' => 'success']);
    }else{
        echo json_encode(['status' => 'error']);
    }
} else {
    echo json_encode(['status' => 'error']);
}
?>