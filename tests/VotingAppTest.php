<?php
require_once __DIR__ . '/../classes/user.php';
require_once __DIR__ . '/../classes/topic.php';
require_once __DIR__ . '/../classes/vote.php';
require_once __DIR__ . '/../classes/comment.php';
require_once __DIR__ . '/../classes/timeformatter.php';

use PHPUnit\Framework\TestCase;

class VotingAppTest extends TestCase {
    private $pdo;

    protected function setUp(): void {
        // Set up an in-memory SQLite database for testing
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create required tables for testing
        $this->pdo->exec("
            CREATE TABLE Users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL
            );
        ");
        $this->pdo->exec("
            CREATE TABLE Topics (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                description TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ");
        $this->pdo->exec("
            CREATE TABLE Votes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                topic_id INTEGER NOT NULL,
                vote_type TEXT CHECK(vote_type IN ('up', 'down')),
                voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ");
        $this->pdo->exec("
            CREATE TABLE Comments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                topic_id INTEGER NOT NULL,
                comment TEXT NOT NULL,
                commented_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }

    public function testUserRegistrationAndAuthentication(): void {
        $user = new User($this->pdo);

        // Test registration success
        $result = $user->registerUser('testuser', 'testuser@example.com', 'securepassword');
        $this->assertTrue($result, 'User registration should succeed.');

        // Test registration failure (duplicate username or email)
        $result = $user->registerUser('testuser', 'testuser@example.com', 'securepassword');
        $this->assertFalse($result, 'Duplicate registration should fail.');

        // Test authentication success
        $authResult = $user->authenticateUser('testuser', 'securepassword');
        $this->assertTrue($authResult, 'Authentication should succeed with correct credentials.');

        // Test authentication failure
        $authResult = $user->authenticateUser('testuser', 'wrongpassword');
        $this->assertFalse($authResult, 'Authentication should fail with incorrect password.');
    }

    public function testTopicCreationAndRetrieval(): void {
        $topic = new Topic($this->pdo);

        // Insert a test user for foreign key constraint
        $this->pdo->exec("INSERT INTO Users (username, email, password) VALUES ('testuser', 'testuser@example.com', 'password')");

        // Test topic creation
        $result = $topic->createTopic(1, 'Test Topic', 'Test Description');
        $this->assertTrue($result, 'Topic creation should succeed.');

        // Test retrieving all topics
        $topics = $topic->getTopics();
        $this->assertCount(1, $topics, 'There should be one topic.');
        $this->assertEquals('Test Topic', $topics[0]['title'], 'The topic title should match.');
    }

    public function testVotingAndHistory(): void {
        $vote = new Vote($this->pdo);

        // Insert test data
        $this->pdo->exec("INSERT INTO Users (username, email, password) VALUES ('testuser', 'testuser@example.com', 'password')");
        $this->pdo->exec("INSERT INTO Topics (user_id, title, description) VALUES (1, 'Test Topic', 'Test Description')");

        // Test voting
        $result = $vote->vote(1, 1, 'up');
        $this->assertTrue($result, 'Voting should succeed.');

        // Test duplicate voting prevention
        $result = $vote->vote(1, 1, 'up');
        $this->assertFalse($result, 'Duplicate voting should fail.');

        // Test voting history
        $history = $vote->getUserVoteHistory(1);
        $this->assertCount(1, $history, 'There should be one vote in the history.');
    }

    public function testComments(): void {
        $comment = new Comment($this->pdo);

        // Insert test data
        $this->pdo->exec("INSERT INTO Users (username, email, password) VALUES ('testuser', 'testuser@example.com', 'password')");
        $this->pdo->exec("INSERT INTO Topics (user_id, title, description) VALUES (1, 'Test Topic', 'Test Description')");

        // Test adding a comment
        $result = $comment->addComment(1, 1, 'This is a test comment.');
        $this->assertTrue($result, 'Adding a comment should succeed.');

        // Test retrieving comments
        $comments = $comment->getComments(1);
        $this->assertCount(1, $comments, 'There should be one comment.');
        $this->assertEquals('This is a test comment.', $comments[0]['comment'], 'The comment text should match.');
    }

    public function testTimeFormatter(): void {
        // Test recent timestamps
        $recentTimestamp = time() - 60; // 1 minute ago
        $result = TimeFormatter::formatTimestamp($recentTimestamp);
        $this->assertStringContainsString('minute', $result, 'TimeFormatter should return relative time for recent timestamps.');

        // Test old timestamps
        $oldTimestamp = strtotime('2023-01-01');
        $result = TimeFormatter::formatTimestamp($oldTimestamp);
        $this->assertEquals('Jan 01, 2023', $result, 'TimeFormatter should return formatted date for old timestamps.');
    }
}
