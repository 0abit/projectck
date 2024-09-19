<?php

include 'components/connect.php';

// Check if the tutor is logged in and set the tutor_id
if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id']; // Assuming user_id is also stored in cookies
} else {
    // Redirect to login page if not logged in
    header('location:login.php');
    exit;
}

// Fetch notifications from the notifications table
$select_notifications = $conn->prepare("SELECT * FROM `notifications` ORDER BY `created_at` DESC");
$select_notifications->execute();
$notifications = $select_notifications->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Notifications</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        .notifications-container {
            margin: 20px auto;
            padding: 20px;

            border-radius: 10px;
        }
        .notifications-container table {
            width: 100%;
            border-collapse: collapse;
        }
        .notifications-container th, .notifications-container td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .notifications-container th {
            background-color: #4CAF50;
            color: white;
        }
        .notifications-container tr:hover {
            background-color: #f1f1f1;
        }
        .notifications-container td {
            font-size: 1.8rem; /* Match this with your button font size */
        }
        .notifications-container th {
            font-size: 2rem; /* Increased size for table headers */
            font-weight: bold;
        }
        .heading {
            text-align: center;
            margin-bottom: 20px;
            font-size: 2.5rem;
        }
        .read-more-btn {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
    </style>

</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <section class="notifications">

        <h1 class="heading">Notifications</h1>

        <div class="notifications-container">
            <?php if (count($notifications) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($notifications as $notification): ?>
                            <?php
                                $truncated_message = strlen($notification['message']) > 50 ? substr($notification['message'], 0, 50) . '...' : $notification['message'];
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($notification['title']) ?></td>
                                <td><?= htmlspecialchars($truncated_message) ?></td>
                                <td><?= htmlspecialchars($notification['created_at']) ?></td>
                                <td>
                                    <a href="view_notification_detail.php?id=<?= htmlspecialchars($notification['id']) ?>" class="read-more-btn">Read More</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No notifications found.</p>
            <?php endif; ?>
        </div>

    </section>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>
