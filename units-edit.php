<?php
require_once __DIR__ . '/layouts/config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['unit_id'])) {
    $unit_id = intval($_POST['unit_id']);
    // Only allow these fields to be updated
    $fields = [
        'project_title', 'project_site', 'phase', 'block', 'lot', 'lot_class'
    ];
    $sets = [];
    foreach ($fields as $f) {
        if (isset($_POST[$f])) {
            $val = mysqli_real_escape_string($link, $_POST[$f]);
            $sets[] = "$f = '$val'";
        }
    }
    if ($sets) {
        $sql = "UPDATE units SET " . implode(',', $sets) . " WHERE id = $unit_id LIMIT 1";
        $ok = mysqli_query($link, $sql);
        if ($ok) {
            $_SESSION['success_message'] = "Unit updated successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to update unit.";
        }
    }
}
header("Location: products-kanban.php");
exit;
?>
