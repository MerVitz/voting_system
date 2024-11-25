<?
class Topic {
    private $pdo;

    // Constructor: receives a PDO object
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Add a new topic to the database
    public function createTopic($userId, $title, $description): bool {
        // Validation
        if (empty($title) || empty($description)) {
            return false;
        }

        try {
            $sql = "INSERT INTO Topics (user_id, title, description) VALUES (:user_id, :title, :description)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':user_id' => $userId,
                ':title' => $title,
                ':description' => $description,
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Retrieve a list of all topics
    public function getTopics(): array {
        $sql = "SELECT id, title, description FROM Topics ORDER BY created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retrieve a list of topics created by a specific user
    public function getCreatedTopics($userId): array {
        $sql = "SELECT id, title, description FROM Topics WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
