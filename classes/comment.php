<?php

class Comment {
    
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function addComment($user_id, $topic_id, $comment): bool {
        // Validate input
        if (empty($user_id) || empty($topic_id) || empty($comment)) {
            return false;
        }

        // Insert comment into the database
        $stmt = $this->pdo->prepare("INSERT INTO comments (user_id, topic_id, comment, commented_at) 
                                     VALUES (:user_id, :topic_id, :comment, NOW())");
        return $stmt->execute([
            ':user_id' => $user_id,
            ':topic_id' => $topic_id,
            ':comment' => $comment
        ]);
    }

    public function getComments($title): array {
        // Validate input
        if (empty($title)) {
            return [];
        }

        // Retrieve comments for the given topic ID
        $stmt = $this->pdo->prepare("SELECT user_id, comment, commented_at 
                                     FROM comments 
                                     WHERE topic_id = :topic_id 
                                     ORDER BY commented_at DESC");
        $stmt->execute([':topic_id' => $title]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

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