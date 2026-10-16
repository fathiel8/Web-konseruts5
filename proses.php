<?php
date_default_timezone_set("Asia/Jakarta");

// 1. Memastikan data dikirim melalui form POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

// 2. Mengambil data dari form
$customerName = trim($_POST["customer_name"] ?? "");
$ticketType   = trim($_POST["ticket_type"] ?? "");
$priceInput   = $_POST["price"] ?? "";
$quantityInput = $_POST["quantity"] ?? "";

// 3. Validasi data
$errors = [];

if ($customerName === "") {
    $errors[] = "Nama pelanggan wajib diisi.";
}

if ($ticketType === "") {
    $errors[] = "Kategori tiket wajib dipilih.";
}

if ($priceInput === "" || !is_numeric($priceInput) || (float)$priceInput <= 0) {
    $errors[] = "Harga tiket tidak valid.";
}

if (
    $quantityInput === "" ||
    filter_var($quantityInput, FILTER_VALIDATE_INT) === false ||
    (int)$quantityInput < 1
) {
    $errors[] = "Jumlah tiket minimal 1.";
}

// 4. Menampilkan pesan jika validasi gagal
if (!empty($errors)) {
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - John Mayer Concert</title>
    <style>
        body {
            margin: 0;
            padding: 30px;
            min-height: 100vh;
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #080808;
            color: white;
            font-family: Arial, sans-serif;
        }
        .box {
            width: 100%;
            max-width: 600px;
            padding: 35px;
            background: #171717;
            border: 1px solid #333;
            border-radius: 12px;
        }
        h1 { color: #d4af37; }
        li { margin: 10px 0; }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            background: #d4af37;
            color: #000;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>Something Went Wrong</h1>
        <p>Silakan periksa kembali data pemesanan tiket.</p>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></li>
            <?php endforeach; ?>
        </ul>
        <a href="index.php">Kembali ke Pemesanan</a>
    </div>
</body>
</html>
<?php
    exit;
}

// 5. Mengonversi data menjadi angka
$price = (float)$priceInput;
$quantity = (int)$quantityInput;

// 6. Menghitung subtotal
$subtotal = $price * $quantity;

// 7. Menghitung diskon 10% jika subtotal minimal Rp100.000
$discount = 0;

if ($subtotal >= 100000) {
    $discount = $subtotal * 0.10;
} else {
    $discount = 0;
}

// 8. Menghitung total pembayaran
$finalTotal = $subtotal - $discount;

// 9. Mengambil tanggal dan waktu transaksi
$transactionDate = date("d-m-Y");
$transactionTime = date("H:i:s");

// 10. Fungsi untuk format mata uang rupiah
function rupiah($value) {
    return "Rp " . number_format($value, 0, ",", ".");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Confirmation | John Mayer</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px 15px;
            min-height: 100vh;
            background: #080808;
            color: #fff;
            font-family: Arial, Helvetica, sans-serif;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 35px;
            background: #151515;
            border: 1px solid #333;
            border-radius: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header-label {
            color: #d4af37;
            letter-spacing: 4px;
            font-size: 12px;
        }

        h1 {
            font-size: 38px;
            letter-spacing: 3px;
        }

        .header h2 {
            color: #aaa;
            font-size: 16px;
            font-weight: normal;
        }

        .success {
            padding: 15px;
            margin-bottom: 25px;
            text-align: center;
            color: #d4af37;
            background: #29230f;
            border: 1px solid #655522;
            border-radius: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 14px 8px;
            border-bottom: 1px solid #333;
        }

        td:first-child {
            color: #aaa;
        }

        td:last-child {
            text-align: right;
            font-weight: bold;
        }

        .discount {
            color: #67d17a;
        }

        .total-row td {
            padding-top: 22px;
            border-bottom: none;
        }

        .total {
            color: #d4af37;
            font-size: 22px;
        }

        .transaction {
            margin-top: 25px;
            padding: 20px;
            background: #101010;
            border: 1px solid #333;
            border-radius: 8px;
        }

        .transaction h3 {
            color: #d4af37;
            font-size: 13px;
            letter-spacing: 2px;
        }

        .transaction p {
            color: #aaa;
            line-height: 1.6;
        }

        .transaction strong {
            color: #fff;
        }

        .discount-box {
            margin-top: 20px;
            padding: 15px;
            text-align: center;
            color: #67d17a;
            background: #102014;
            border: 1px solid #285b32;
            border-radius: 6px;
        }

        .no-discount {
            color: #ccc;
            background: #202020;
            border-color: #444;
        }

        .buttons {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .button {
            flex: 1;
            padding: 14px;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
        }

        .gold-button {
            background: #d4af37;
            color: #000;
        }

        .dark-button {
            background: #333;
            color: #fff;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #777;
            font-size: 12px;
        }

        @media (max-width: 600px) {
            .container { padding: 22px; }
            h1 { font-size: 28px; }
            td { font-size: 13px; }
            .buttons { flex-direction: column; }
        }

        @media print {
            body {
                padding: 0;
                background: #fff;
                color: #000;
            }

            .container {
                margin: 0 auto;
                background: #fff;
                color: #000;
                border: none;
            }

            .transaction p,
            td:first-child,
            .footer {
                color: #444;
            }

            .transaction strong {
                color: #000;
            }

            .buttons {
                display: none;
            }
        }
    </style>
</head>

<body>
    <main class="container">
        <header class="header">
            <p class="header-label">CONCERTLIVE</p>
            <h1>JOHN MAYER</h1>
            <h2>Solo Acoustic Tour</h2>
        </header>

        <div class="success">
            &#10003; TICKET ORDER SUCCESSFULLY PROCESSED
        </div>

        <h3>Ticket Order Details</h3>

        <table>
            <tr>
                <td>Customer Name</td>
                <td><?= htmlspecialchars($customerName, ENT_QUOTES, "UTF-8") ?></td>
            </tr>
            <tr>
                <td>Ticket Category</td>
                <td><?= htmlspecialchars($ticketType, ENT_QUOTES, "UTF-8") ?></td>
            </tr>
            <tr>
                <td>Ticket Price</td>
                <td><?= rupiah($price) ?></td>
            </tr>
            <tr>
                <td>Quantity</td>
                <td><?= $quantity ?> Ticket(s)</td>
            </tr>
            <tr>
                <td>Subtotal</td>
                <td><?= rupiah($subtotal) ?></td>
            </tr>
            <tr>
                <td>Discount</td>
                <td class="discount">
                    <?= $discount > 0 ? "- " . rupiah($discount) : rupiah(0) ?>
                </td>
            </tr>
            <tr class="total-row">
                <td><strong>TOTAL PAYMENT</strong></td>
                <td class="total"><?= rupiah($finalTotal) ?></td>
            </tr>
        </table>

        <?php if ($discount > 0): ?>
            <div class="discount-box">
                Selamat! Kamu mendapatkan diskon 10%
                karena subtotal mencapai Rp100.000.
            </div>
        <?php else: ?>
            <div class="discount-box no-discount">
                Belum mendapatkan diskon. Diskon 10% berlaku
                untuk subtotal minimal Rp100.000.
            </div>
        <?php endif; ?>

        <section class="transaction">
            <h3>TRANSACTION INFORMATION</h3>

            <p>
                Transaction Date:
                <strong><?= $transactionDate ?></strong>
            </p>

            <p>
                Transaction Time:
                <strong><?= $transactionTime ?></strong>
            </p>

            <p>
                Event:
                <strong>John Mayer - Solo Acoustic Tour</strong>
            </p>

            <p>
                Venue:
                <strong>Jakarta International Stadium</strong>
            </p>

            <p>
                Location:
                <strong>Jakarta, Indonesia</strong>
            </p>
        </section>

        <div class="buttons">
            <a href="index.php" class="button dark-button">
                BUY ANOTHER TICKET
            </a>

            <a href="#" onclick="window.print(); return false;"
               class="button gold-button">
                PRINT RECEIPT
            </a>
        </div>

        <footer class="footer">
            CONCERTLIVE &copy; 2026<br>
            John Mayer — Live in Jakarta
        </footer>
    </main>
</body>
</html>
