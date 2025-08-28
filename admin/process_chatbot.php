<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied.");
}
require_once '../includes/db_conn.php';

if (isset($_POST['save_qa'])) {
    $qa_id = (int)$_POST['qa_id'];
    $question = $_POST['question'];
    $answer = $_POST['answer'];

    if ($qa_id > 0) {
        // Update
        $stmt = $conn->prepare("UPDATE chatbot_qa SET question = ?, answer = ? WHERE id = ?");
        $stmt->bind_param("ssi", $question, $answer, $qa_id);
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO chatbot_qa (question, answer) VALUES (?, ?)");
        $stmt->bind_param("ss", $question, $answer);
    }

    if ($stmt->execute()) {
        header('Location: manage_chatbot.php?status=success');
    } else {
        header('Location: manage_chatbot.php?status=error');
    }
    $stmt->close();
    exit();
}
?>