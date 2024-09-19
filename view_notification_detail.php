<?php

include 'components/connect.php';

// Check if the user is logged in and set the user_id
if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id']; // Assuming user_id is also stored in cookies
} else {
    // Redirect to login page if not logged in
    header('location:login.php');
    exit;
}

// Check if the notification ID is set
if (isset($_GET['id'])) {
    $notification_id = $_GET['id'];

    // Fetch the notification details
    $select_notification = $conn->prepare("SELECT * FROM `notifications` WHERE id = ?");
    $select_notification->execute([$notification_id]);
    $notification = $select_notification->fetch(PDO::FETCH_ASSOC);

    if (!$notification) {
        // Redirect to notifications page if the notification is not found
        header('location:view_notification.php');
        exit;
    }
} else {
    // Redirect to notifications page if no ID is provided
    header('location:view_notification.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Details</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        .notification-detail-container {
            margin: 20px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            word-wrap: break-word; /* Ensures long words break to the next line */
        }
        .notification-detail-container h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        .notification-detail-container p {
            font-size: 1.8rem;
            margin-bottom: 10px;
            color: #555;
            line-height: 1.6;
            word-wrap: break-word; /* Ensures long words break to the next line */
        }
        .notification-detail-container .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .notification-detail-container .back-btn:hover {
            background-color: #45a049;
        }
        .notification-meta {
            font-size: 1.4rem;
            color: #999;
            margin-bottom: 20px;
        }
    </style>

</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <section class="notification-detail">

        <div class="notification-detail-container">
            <h2><?= htmlspecialchars($notification['title']) ?></h2>
            <p class="notification-meta"><strong>Date:</strong> <?= htmlspecialchars($notification['created_at']) ?></p>
            <p><?= nl2br(htmlspecialchars($notification['message'])) ?></p>
            <a href="view_notification.php" class="back-btn">Back to Notifications</a>
        </div>

    </section>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>
