<?php
require_once __DIR__ . '/layouts/config.php'; // Adjust if config is inside layouts folder
require_once __DIR__ . '/libs/barcode/BarcodeGenerator.php';
require_once __DIR__ . '/libs/barcode/BarcodeGeneratorPNG.php';

/** simple PSR-4 loader for Picqer\Barcode\… */
spl_autoload_register(function ($class) {
    $prefix    = 'Picqer\\Barcode\\';
    $baseDir   = __DIR__ . '/libs/barcode/';   // adjust if needed

    // class does not belong to this namespace
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    // convert the remainder of the class name to a file path
    $relative = substr($class, strlen($prefix));
    $file     = $baseDir . str_replace('\\', '/', $relative) . '.php';

    if (is_file($file)) {
        require $file;
    }
});


use Picqer\Barcode\BarcodeGeneratorPNG;

$query = "SELECT name, price, barcode FROM products ORDER BY created_at DESC";
$result = mysqli_query($link, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product List with Barcodes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card img {
            max-width: 100%;
            height: auto;
        }
        .barcode {
            display: block;
            margin-top: 10px;
            background: #f8f8f8;
            padding: 5px;
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4 text-center">Product List with Barcodes</h2>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">

        <?php
        if (mysqli_num_rows($result) > 0) {
            $generator = new BarcodeGeneratorPNG();
            while ($row = mysqli_fetch_assoc($result)) {
                $barcode = $row['barcode'] ?? '';
                $barcodeImage = '';
                if (!empty($barcode)) {
                    $barcodeImage = '<img class="barcode" src="data:image/png;base64,' . base64_encode(
                        $generator->getBarcode($barcode, $generator::TYPE_CODE_128)
                    ) . '" alt="Barcode">';
                }

                echo '<div class="col">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title">' . htmlspecialchars($row['name']) . '</h5>
                                <p class="card-text"><strong>₱' . number_format($row['price'], 2) . '</strong></p>
                                ' . $barcodeImage . '
                            </div>
                        </div>
                      </div>';
            }
        } else {
            echo '<p>No products found.</p>';
        }
        ?>

    </div>
</div>

</body>
</html>
