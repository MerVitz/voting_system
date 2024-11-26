<?php
class Vote {
    private $pdo;

    // Constructor: receives a PDO object
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Record a user's vote on a topic
    public function vote($userId, $topicId, $voteType): bool {
        // Check if the user has already voted
        if ($this->hasVoted($topicId, $userId)) {
            return false;
        }

        try {
            $sql = "INSERT INTO Votes (user_id, topic_id, vote_type) VALUES (:user_id, :topic_id, :vote_type)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':user_id' => $userId,
                ':topic_id' => $topicId,
                ':vote_type' => $voteType,
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Check if a user has already voted on a topic
    public function hasVoted($topicId, $userId): bool {
        $sql = "SELECT COUNT(*) FROM Votes WHERE user_id = :user_id AND topic_id = :topic_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':topic_id' => $topicId,
        ]);
        return $stmt->fetchColumn() > 0;
    }

    // Retrieve the voting history of a user
    public function getUserVoteHistory($userId): array {
        $sql = "SELECT topic_id, vote_type, voted_at FROM Votes WHERE user_id = :user_id ORDER BY voted_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
