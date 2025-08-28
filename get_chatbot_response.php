<?php
header('Content-Type: application/json');
require_once 'includes/db_conn.php';

$action = $_GET['action'] ?? '';

if ($action === 'get_questions') {
    $result = $conn->query("SELECT id, question FROM chatbot_qa");
    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
    echo json_encode($questions);
    exit();
}

if ($action === 'get_answer' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT answer FROM chatbot_qa WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $answer = $result->fetch_assoc();
    
    if ($answer) {
        echo json_encode($answer);
    } else {
        echo json_encode(['answer' => 'Maaf, saya tidak dapat menemukan jawaban untuk pertanyaan itu.']);
    }
    $stmt->close();
    exit();
}

// Default response jika action tidak valid
echo json_encode(['error' => 'Invalid action']);
?>