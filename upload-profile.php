<?php
require_once __DIR__ . '/layouts/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image']) && isset($_POST['contact_id'])) {
    $contact_id = intval($_POST['contact_id']);
    if ($contact_id <= 0) {
        echo json_encode(['status'=>'error', 'message'=>'Invalid contact ID']);
        exit;
    }
    $file = $_FILES['profile_image'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status'=>'error', 'message'=>'Upload error']);
        exit;
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','gif'])) {
        echo json_encode(['status'=>'error', 'message'=>'Only image files allowed']);
        exit;
    }
    $filename = "profile_{$contact_id}_" . time() . ".$ext";
    $dest = __DIR__ . "/uploads/contacts/$filename";
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        echo json_encode(['status'=>'error', 'message'=>'Failed to save image']);
        exit;
    }
    // Update DB
    $stmt = $link->prepare("UPDATE contacts SET profile_image=? WHERE id=?");
    $stmt->bind_param('si', $filename, $contact_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['status'=>'success']);
    exit;
}
echo json_encode(['status'=>'error','message'=>'No file uploaded']);
exit;
?>
