<?php
// Include required classes and ensure user authentication
include './classes/topic.php';
include './classes/vote.php';
include './classes/comment.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$topicId = $_GET['id'];
$topic = new Topic($pdo);
$topicDetails = $topic->getTopics($topicId);

$comment = new Comment($pdo);
$comments = $comment->getComments($topicId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Topic Details</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($topicDetails['title']) ?></h1>
        <p><?= htmlspecialchars($topicDetails['description']) ?></p>

        <h2>Vote</h2>
        <form action="vote_process.php" method="POST">
            <input type="hidden" name="topic_id" value="<?= $topicId ?>">
            <button name="vote_type" value="up">Upvote</button>
            <button name="vote_type" value="down">Downvote</button>
        </form>

        <h2>Comments</h2>
        <ul>
            <?php foreach ($comments as $comment): ?>
                <li><?= htmlspecialchars($comment['comment']) ?></li>
            <?php endforeach; ?>
        </ul>

        <form action="add_comment.php" method="POST">
            <textarea name="comment" required></textarea>
            <input type="hidden" name="topic_id" value="<?= $topicId ?>">
            <button type="submit">Add Comment</button>
        </form>
    </div>
</body>
</html>
