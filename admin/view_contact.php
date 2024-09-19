<?php

include '../components/connect.php';

// Check if the tutor is logged in and set the tutor_id
if (isset($_COOKIE['tutor_id'])) {
    $tutor_id = $_COOKIE['tutor_id'];
} else {
    // Redirect to login page if not logged in
    header('location:login.php');
    exit;
}

// Fetch messages from the contact table
$select_messages = $conn->prepare("SELECT * FROM `contact` ORDER BY `created_at` DESC");
$select_messages->execute();
$messages = $select_messages->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Messages</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">
    <style>
        .messages-container td {
            font-size: 1.8rem; /* Match this with your button font size */
        }
        .messages-container th {
            font-size: 2rem; /* Increased size for table headers */
            font-weight: bold;
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

    <?php include '../components/admin_header.php'; ?>

    <section class="messages">

        <h1 class="heading">User Messages</h1>

        <div class="messages-container">
            <?php if (count($messages) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Number</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $message): ?>
                            <?php
                                $truncated_message = strlen($message['message']) > 50 ? substr($message['message'], 0, 50) . '...' : $message['message'];
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($message['id']) ?></td>
                                <td><?= htmlspecialchars($message['name']) ?></td>
                                <td><?= htmlspecialchars($message['email']) ?></td>
                                <td><?= htmlspecialchars($message['number']) ?></td>
                                <td><?= htmlspecialchars($truncated_message) ?></td>
                                <td><?= htmlspecialchars($message['created_at']) ?></td>
                                <td>
                                    <a href="view_contact_detail.php?id=<?= htmlspecialchars($message['id']) ?>" class="read-more-btn">Read More</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No messages found.</p>
            <?php endif; ?>
        </div>

    </section>

    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

</body>

</html>
