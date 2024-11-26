<?php
// Include the Topic class and ensure the user is authenticated
include './classes/topic.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$topic = new Topic($pdo);
$topics = $topic->getTopics();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to the Voting App</h1>
        <a href="create_topic.php" class="btn">Add New Topic</a>
        <h2>Topics</h2>
        <ul>
            <?php foreach ($topics as $topic): ?>
                <li>
                    <a href="topic_details.php?id=<?= $topic['id'] ?>">
                        <?= htmlspecialchars($topic['title']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>