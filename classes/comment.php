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