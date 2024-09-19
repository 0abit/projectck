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

// Check if the message ID is set
if (isset($_GET['id'])) {
    $message_id = $_GET['id'];

    // Fetch the message details
    $select_message = $conn->prepare("SELECT * FROM `contact` WHERE id = ?");
    $select_message->execute([$message_id]);
    $message = $select_message->fetch(PDO::FETCH_ASSOC);

    if (!$message) {
        // Redirect to messages page if the message is not found
        header('location:view_contact.php');
        exit;
    }
} else {
    // Redirect to messages page if no ID is provided
    header('location:view_contact.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Details</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">
    <style>
        .message-detail-container {
            margin: 20px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            word-wrap: break-word; /* Ensures long words break to the next line */
        }
        .message-detail-container h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        .message-detail-container p {
            font-size: 1.8rem;
            margin-bottom: 10px;
            color: #555;
            line-height: 1.6;
            word-wrap: break-word; /* Ensures long words break to the next line */
        }
        .message-detail-container .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .message-detail-container .back-btn:hover {
            background-color: #45a049;
        }
        .message-meta {
            font-size: 1.4rem;
            color: #999;
            margin-bottom: 20px;
        }
    </style>

</head>

<body>

    <?php include '../components/admin_header.php'; ?>

    <section class="message-detail">

        <div class="message-detail-container">
            <h2><?= htmlspecialchars($message['name']) ?></h2>
            <p class="message-meta"><strong>Email:</strong> <?= htmlspecialchars($message['email']) ?></p>
            <p class="message-meta"><strong>Number:</strong> <?= htmlspecialchars($message['number']) ?></p>
            <p class="message-meta"><strong>Date:</strong> <?= htmlspecialchars($message['created_at']) ?></p>
            <p><?= nl2br(htmlspecialchars($message['message'])) ?></p>
            <a href="view_contact.php" class="back-btn">Back to Messages</a>
        </div>

    </section>

    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

</body>

</html>
