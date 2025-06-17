<?php
require_once __DIR__ . '/layouts/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['unit_id'])) {
    $unit_id = intval($_POST['unit_id']);
    $ok = false;

    // Only allow delete if unit is still available
    $check = $link->prepare("SELECT status FROM units WHERE id=? LIMIT 1");
    $check->bind_param('i', $unit_id);
    $check->execute();
    $check->bind_result($status);
    if ($check->fetch() && strtolower($status) == 'available') {
        $check->close();

        // Proceed with deletion
        $stmt = $link->prepare("DELETE FROM units WHERE id=? LIMIT 1");
        $stmt->bind_param('i', $unit_id);
        $ok = $stmt->execute();
        $stmt->close();

        $_SESSION['success_message'] = $ok ? "Unit deleted successfully." : "Delete failed.";
    } else {
        $_SESSION['error_message'] = "Only available units can be deleted.";
        $check->close();
    }
} else {
    $_SESSION['error_message'] = "Invalid unit selected.";
}

header("Location: products-kanban.php");
exit;
?>
