<?
class User {
    private $pdo;

    // Constructor: receives a PDO object
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Register a new user in the database
    public function registerUser($username, $email, $password): bool {
        // Validation checks
        if (empty($username) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 9) {
            return false;
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into the database
        try {
            $sql = "INSERT INTO Users (username, email, password) VALUES (:username, :email, :password)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashedPassword,
            ]);
        } catch (PDOException $e) {
            // Registration fails if username or email is already taken
            return false;
        }
    }

    // Authenticate a user
    public function authenticateUser($username, $password): bool {
        $sql = "SELECT password FROM Users WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if ($user && password_verify($password, $user['password'])) {
            return true;
        }
        return false;
    }
}
