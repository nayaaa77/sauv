<?php 
include 'includes/header_admin.php'; 

$qa_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$question = '';
$answer = '';
$form_title = "Add New Q&A";
$button_text = "Add Q&A";

if ($qa_id > 0) {
    $stmt = $conn->prepare("SELECT question, answer FROM chatbot_qa WHERE id = ?");
    $stmt->bind_param("i", $qa_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $qa_data = $result->fetch_assoc();
        $question = $qa_data['question'];
        $answer = $qa_data['answer'];
        $form_title = "Edit Q&A";
        $button_text = "Update Q&A";
    }
    $stmt->close();
}
?>

<script>document.querySelector('.header-title').textContent = '<?php echo $form_title; ?>';</script>

<div class="page-header">
    <h2><?php echo $form_title; ?></h2>
    <a href="manage_chatbot.php" class="btn">Back to List</a>
</div>

<div class="card">
    <div class="card-body" style="padding: 25px;">
        <form action="process_chatbot.php" method="POST">
            <input type="hidden" name="qa_id" value="<?php echo $qa_id; ?>">
            <div class="form-group">
                <label for="question">Question</label>
                <input type="text" id="question" name="question" class="form-control" value="<?php echo htmlspecialchars($question); ?>" required>
            </div>
            <div class="form-group">
                <label for="answer">Answer</label>
                <textarea id="answer" name="answer" rows="6" class="form-control" required><?php echo htmlspecialchars($answer); ?></textarea>
            </div>
            <button type="submit" name="save_qa" class="btn btn-primary"><?php echo $button_text; ?></button>
        </form>
    </div>
</div>

<?php include 'includes/footer_admin.php'; ?>