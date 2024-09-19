<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
   header('location:login.php');
   exit();
}

if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = '';
   header('location:home.php');
   exit();
}

// Handle comment form submission
if(isset($_POST['add_comment'])){
   $content_id = $_POST['content_id'];
   $comment_box = $_POST['comment_box'];
   $comment_box = filter_var($comment_box, FILTER_SANITIZE_STRING);

   $insert_comment = $conn->prepare("INSERT INTO `comments` (user_id, content_id, comment) VALUES (?, ?, ?)");
   $insert_comment->execute([$user_id, $content_id, $comment_box]);

   $message[] = 'Comment added successfully!';
}

// Handle comment update form submission
if(isset($_POST['update_comment'])){
   $comment_id = $_POST['comment_id'];
   $update_box = $_POST['update_box'];
   $update_box = filter_var($update_box, FILTER_SANITIZE_STRING);

   $update_comment = $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ? AND user_id = ?");
   $update_comment->execute([$update_box, $comment_id, $user_id]);

   $message[] = 'Comment updated successfully!';
}

// Handle comment deletion
if(isset($_POST['delete_comment'])){
   $comment_id = $_POST['comment_id'];
   $comment_id = filter_var($comment_id, FILTER_SANITIZE_STRING);

   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ? AND user_id = ?");
   $verify_comment->execute([$comment_id, $user_id]);

   if($verify_comment->rowCount() > 0){
      $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
      $delete_comment->execute([$comment_id]);
      echo json_encode(['status' => 'success']);
   }else{
      echo json_encode(['status' => 'error']);
   }
   exit();
}

$select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? AND status = ?");
$select_content->execute([$get_id, 'active']);
if($select_content->rowCount() > 0){
   $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);
   $content_id = $fetch_content['id'];
   $playlist_id = $fetch_content['playlist_id'];

   // Get next video
   $next_video = $conn->prepare("SELECT id FROM `content` WHERE playlist_id = ? AND id > ? ORDER BY id ASC LIMIT 1");
   $next_video->execute([$playlist_id, $content_id]);
   $next_video = $next_video->fetch(PDO::FETCH_ASSOC);

   // Get previous video
   $prev_video = $conn->prepare("SELECT id FROM `content` WHERE playlist_id = ? AND id < ? ORDER BY id DESC LIMIT 1");
   $prev_video->execute([$playlist_id, $content_id]);
   $prev_video = $prev_video->fetch(PDO::FETCH_ASSOC);

   // Check if video is completed
   $is_completed = $conn->prepare("SELECT * FROM `video_progress` WHERE user_id = ? AND content_id = ? AND completed = 1");
   $is_completed->execute([$user_id, $content_id]);
   $video_completed = $is_completed->rowCount() > 0;

   // Get playlist progress
   $total_videos = $conn->prepare("SELECT COUNT(*) FROM `content` WHERE playlist_id = ?");
   $total_videos->execute([$playlist_id]);
   $total_videos = $total_videos->fetchColumn();

   $completed_videos = $conn->prepare("SELECT COUNT(*) FROM `video_progress` WHERE user_id = ? AND content_id IN (SELECT id FROM `content` WHERE playlist_id = ?) AND completed = 1");
   $completed_videos->execute([$user_id, $playlist_id]);
   $completed_videos = $completed_videos->fetchColumn();

   $progress = min(($completed_videos / $total_videos) * 100, 100); // Limit progress to 100%

   $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE content_id = ?");
   $select_likes->execute([$content_id]);
   $total_likes = $select_likes->rowCount();  

   $verify_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND content_id = ?");
   $verify_likes->execute([$user_id, $content_id]);

   $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ? LIMIT 1");
   $select_tutor->execute([$fetch_content['tutor_id']]);
   $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?= $fetch_content['title']; ?> | Watch Video</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <style>
      .add-comment, .edit-comment-form {
         margin-top: 10px;
         padding: 10px;
         border: 1px solid #ccc;
         border-radius: 5px;
         background-color: #333;
         box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      }
      .add-comment textarea, .edit-comment-form textarea {
         width: 100%;
         padding: 10px;
         border: 1px solid #ccc;
         border-radius: 5px;
         margin-bottom: 10px;
         resize: vertical;
         font-size: 14px;
         line-height: 1.5;
         background-color: #444;
         color: #fff;
      }
      .add-comment .inline-btn, .edit-comment-form .inline-btn, .edit-comment-form .inline-option-btn {
         margin-right: 10px;
         padding: 10px 20px;
         border-radius: 5px;
         cursor: pointer;
         transition: background-color 0.3s ease;
      }
      .add-comment .inline-btn, .edit-comment-form .inline-btn {
         background-color: #4CAF50;
         color: white;
         border: none;
      }
      .add-comment .inline-btn:hover, .edit-comment-form .inline-btn:hover {
         background-color: #45a049;
      }
      .edit-comment-form .inline-option-btn {
         background-color: #f44336;
         color: white;
         border: none;
      }
      .edit-comment-form .inline-option-btn:hover {
         background-color: #e53935;
      }
   </style>
</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- watch video section starts  -->

<section class="watch-video">
   <h1 class="heading"><?= $fetch_content['title']; ?></h1>

   <div class="video-details">
      <video src="uploaded_files/<?= $fetch_content['video']; ?>" class="video" poster="uploaded_files/<?= $fetch_content['thumb']; ?>" controls></video>
      <div class="info">
         <p><i class="fas fa-calendar"></i><span><?= $fetch_content['date']; ?></span></p>
         <p><i class="fas fa-heart"></i><span id="likes-count"><?= $total_likes; ?> likes</span></p>
      </div>
      <div class="tutor">
         <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
         <div>
            <h3><?= $fetch_tutor['name']; ?></h3>
            <span><?= $fetch_tutor['profession']; ?></span>
         </div>
      </div>
      <form action="" method="post" class="flex">
         <input type="hidden" name="content_id" value="<?= $content_id; ?>">
         <button type="submit" name="like_content" class="inline-btn <?= ($verify_likes->rowCount() > 0) ? 'active' : ''; ?>">
            <i class="fas <?= ($verify_likes->rowCount() > 0) ? 'fa-heart' : 'fa-heart-o'; ?>"></i> <?= ($verify_likes->rowCount() > 0) ? 'Unlike' : 'Like'; ?>
         </button>
         <button type="submit" name="complete_video" id="complete-btn" class="inline-btn <?= ($video_completed) ? 'disabled' : ''; ?>">
            <i class="fas <?= ($video_completed) ? 'fa-check-circle' : 'fa-circle'; ?>"></i> <?= ($video_completed) ? 'Completed' : 'Mark as completed'; ?>
         </button>
         <?php if($prev_video): ?>
            <a href="watch_video.php?get_id=<?= $prev_video['id']; ?>" class="inline-option-btn">Previous video</a>
         <?php endif; ?>
         <?php if($next_video): ?>
            <a href="watch_video.php?get_id=<?= $next_video['id']; ?>" class="inline-option-btn">Next video</a>
         <?php else: ?>
            <a href="playlist.php?get_id=<?= $playlist_id; ?>" class="inline-btn">Finish Session</a>
         <?php endif; ?>
      </form>
      <div class="description">
         <h3>Course progress</h3>
         <div class="progress-bar">
            <span class="progress-bar-fill" style="width: <?= $progress; ?>%;"></span>
         </div>
         <p><?= number_format($progress, 2); ?>% completed</p>
      </div>
      <div class="description">
         <h3>Video description</h3>
         <p><?= $fetch_content['description']; ?></p>
      </div>
   </div>
</section>

<!-- watch video section ends -->

<!-- comments section starts  -->

<section class="comments">
   <h1 class="heading">Add a comment</h1>
   <form action="" method="post" class="add-comment">
      <input type="hidden" name="content_id" value="<?= $get_id; ?>">
      <textarea name="comment_box" required placeholder="Write your comment..." maxlength="1000" cols="30" rows="10"></textarea>
      <input type="submit" value="Add comment" name="add_comment" class="inline-btn">
   </form>

   <?php
      $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE content_id = ? ORDER BY date DESC");
      $select_comments->execute([$get_id]);
      $total_comments = $select_comments->rowCount();
   ?>

   <h1 class="heading">User comments (<?= $total_comments; ?>)</h1>

   <div class="show-comments">
      <?php
         if($total_comments > 0){
            while($fetch_comment = $select_comments->fetch(PDO::FETCH_ASSOC)){   
               $select_commentor = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
               $select_commentor->execute([$fetch_comment['user_id']]);
               $fetch_commentor = $select_commentor->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box" id="comment-<?= $fetch_comment['id']; ?>">
         <div class="user">
            <img src="uploaded_files/<?= $fetch_commentor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_commentor['name']; ?></h3>
               <span><?= $fetch_comment['date']; ?></span>
            </div>
         </div>
         <p class="text"><?= $fetch_comment['comment']; ?></p>
         <?php if($fetch_comment['user_id'] == $user_id): ?>
         <form action="" method="post" class="flex-btn delete-comment-form">
            <input type="hidden" name="comment_id" value="<?= $fetch_comment['id']; ?>">
            <button type="button" class="inline-option-btn edit-comment-btn" data-comment-id="<?= $fetch_comment['id']; ?>">Edit</button>
            <button type="submit" name="delete_comment" class="inline-delete-btn" onclick="return confirm('Delete this comment?');">Delete</button>
         </form>
         <form action="" method="post" class="edit-comment-form" id="edit-form-<?= $fetch_comment['id']; ?>" style="display: none;">
            <input type="hidden" name="comment_id" value="<?= $fetch_comment['id']; ?>">
            <textarea name="update_box" required maxlength="1000" cols="30" rows="10"><?= $fetch_comment['comment']; ?></textarea>
            <input type="submit" value="Update comment" name="update_comment" class="inline-btn">
            <button type="button" class="inline-option-btn cancel-edit-btn" data-comment-id="<?= $fetch_comment['id']; ?>">Cancel</button>
         </form>
         <?php endif; ?>
      </div>
      <?php
            }
         }else{
            echo '<p class="empty">No comments added yet!</p>';
         }
      ?>
   </div>
   
</section>

<!-- comments section ends -->

<!-- custom js file link  -->
<script src="js/script.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const video = document.querySelector('.video');
    const completeBtn = document.getElementById('complete-btn');
    const progressBar = document.querySelector('.progress-bar-fill');
    const progressText = document.querySelector('.progress-bar + p');
    const finishSessionBtn = document.querySelector('.inline-btn[href^="playlist.php"]');

    completeBtn.addEventListener('click', function() {
        if (!this.classList.contains('disabled')) {
            const formData = new FormData();
            formData.append('content_id', <?= $content_id; ?>);
            formData.append('playlist_id', <?= $playlist_id; ?>);

            fetch('update_progress.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    this.classList.add('disabled');
                    this.innerHTML = '<i class="fas fa-check-circle"></i> Completed';
                    const progress = Math.min(data.progress, 100); // Limit progress to 100%
                    progressBar.style.width = progress + '%';
                    progressText.textContent = progress.toFixed(2) + '% completed';
                    
                    if (progress === 100 && finishSessionBtn) {
                        finishSessionBtn.style.display = 'inline-block';
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });

    video.addEventListener('ended', function() {
        completeBtn.click();
    });

    // Edit comment functionality
    const editButtons = document.querySelectorAll('.edit-comment-btn');
    const cancelButtons = document.querySelectorAll('.cancel-edit-btn');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const editForm = document.getElementById(`edit-form-${commentId}`);
            editForm.style.display = 'block';
            this.closest('.flex-btn').style.display = 'none';
        });
    });

    cancelButtons.forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const editForm = document.getElementById(`edit-form-${commentId}`);
            editForm.style.display = 'none';
            editForm.previousElementSibling.style.display = 'flex';
        });
    });

    // Like button functionality
    const likeBtn = document.querySelector('.inline-btn[name="like_content"]');
    likeBtn.addEventListener('click', function(event) {
        event.preventDefault();
        const formData = new FormData();
        formData.append('content_id', <?= $content_id; ?>);

        fetch('like_video.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const likesCount = document.getElementById('likes-count');
                likesCount.textContent = data.total_likes + ' likes';
                if (data.liked) {
                    likeBtn.classList.add('active');
                    likeBtn.innerHTML = '<i class="fas fa-heart"></i> Unlike';
                } else {
                    likeBtn.classList.remove('active');
                    likeBtn.innerHTML = '<i class="fas fa-heart-o"></i> Like';
                }
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Delete comment functionality
    const deleteForms = document.querySelectorAll('.delete-comment-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const commentId = this.querySelector('input[name="comment_id"]').value;
            const formData = new FormData(this);

            fetch('delete_comment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const commentBox = document.getElementById(`comment-${commentId}`);
                    if (commentBox) {
                        commentBox.remove();
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
const likeBtn = document.querySelector('.inline-btn[name="like_content"]');
    likeBtn.addEventListener('click', function(event) {
        event.preventDefault();
        const formData = new FormData();
        formData.append('content_id', <?= $content_id; ?>);

        fetch('like_video.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const likesCount = document.getElementById('likes-count');
                likesCount.textContent = data.total_likes + ' likes';
                if (data.liked) {
                    likeBtn.classList.add('active');
                    likeBtn.innerHTML = '<i class="fas fa-heart"></i> Unlike';
                } else {
                    likeBtn.classList.remove('active');
                    likeBtn.innerHTML = '<i class="fas fa-heart-o"></i> Like';
                }
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Delete comment functionality
    const deleteForms = document.querySelectorAll('.delete-comment-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const commentId = this.querySelector('input[name="comment_id"]').value;
            const formData = new FormData(this);

            fetch('delete_comment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const commentBox = document.getElementById(`comment-${commentId}`);
                    if (commentBox) {
                        commentBox.remove();
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
</script>

</body>
</html>
<?php
} else {
    header('location:home.php');
}
?>

