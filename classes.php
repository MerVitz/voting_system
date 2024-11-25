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

<?php
class TimeFormatter {
    public static function formatTimestamp(int $timestamp): string {
        $currentTimestamp = time();
        $difference = $currentTimestamp - $timestamp;

        // If timestamp is within the last 12 months (365 days * 24 hours * 60 minutes * 60 seconds)
        if ($difference < 365 * 24 * 60 * 60) {
            // Handle relative time
            if ($difference < 60) {
                return $difference . " seconds ago";
            } elseif ($difference < 3600) {
                $minutes = floor($difference / 60);
                return $minutes . " minute" . ($minutes > 1 ? "s" : "") . " ago";
            } elseif ($difference < 86400) {
                $hours = floor($difference / 3600);
                return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
            } else {
                $days = floor($difference / 86400);
                return $days . " day" . ($days > 1 ? "s" : "") . " ago";
            }
        } else {
            // Return formatted date for timestamps older than 12 months
            return date("M d, Y", $timestamp);
        }
    }
}
?>

