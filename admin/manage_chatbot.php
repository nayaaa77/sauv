<?php 
$page_title = "Manage Chatbot";
include 'includes/header_admin.php'; 

// Logika untuk menghapus Q&A
if (isset($_POST['delete_qa'])) {
    $qa_id_to_delete = (int)$_POST['qa_id'];
    $stmt = $conn->prepare("DELETE FROM chatbot_qa WHERE id = ?");
    $stmt->bind_param("i", $qa_id_to_delete);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_chatbot.php?delete=success");
    exit();
}
?>

<script>document.querySelector('.header-title').textContent = '<?php echo $page_title; ?>';</script>

<div class="page-header">
    <h2><?php echo $page_title; ?></h2>
    <a href="edit_chatbot.php" class="btn btn-primary">+ Add New Q&A</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table-products">
            <thead>
                <tr>
                    <th class="col-id">ID</th>
                    <th>Question</th>
                    <th>Answer</th>
                    <th class="col-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM chatbot_qa ORDER BY id DESC");
                if ($result && $result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['question']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars(substr($row['answer'], 0, 100))) . '...'; ?></td>
                    <td class="actions">
                        <a href="edit_chatbot.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary">Edit</a>
                        <form action="manage_chatbot.php" method="POST" style="display:inline;">
                            <input type="hidden" name="qa_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete_qa" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this item?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php 
                    endwhile;
                else:
                ?>
                <tr>
                    <td colspan="4" class="no-products">No Q&A found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer_admin.php'; ?>