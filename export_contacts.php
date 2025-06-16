<?php
require_once __DIR__ . '/layouts/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fields'])) {
    $fieldsArr = json_decode($_POST['fields'], true);
    if (!is_array($fieldsArr) || !count($fieldsArr)) {
        http_response_code(400);
        exit('Invalid fields');
    }
    $dbFields = [];
    $headerLabels = [];
    foreach ($fieldsArr as $f) {
        $dbFields[] = preg_replace('/[^a-zA-Z0-9_]/', '', $f['field']);
        $headerLabels[] = $f['label'];
    }
    $fieldListSql = implode(',', array_map(function($f){ return "`$f`"; }, $dbFields));
    $sql = "SELECT $fieldListSql FROM contacts ORDER BY created_at DESC";
    $result = mysqli_query($link, $sql);

    // Send headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=contacts_export.csv');

    $output = fopen('php://output', 'w');
    // Write header
    fputcsv($output, $headerLabels);
    // Write rows
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $csvRow = [];
            foreach ($dbFields as $f) {
                $csvRow[] = $row[$f];
            }
            fputcsv($output, $csvRow);
        }
    }
    fclose($output);
    exit;
}
http_response_code(400);
echo 'Invalid request';

?>