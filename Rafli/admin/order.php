<?php
session_start();
require_once('../config/db_connection.php');

// Pastikan user adalah admin
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil data dari tabel history
$query_history = "SELECT * FROM history ORDER BY Tanggal_History DESC";
$result_history = $conn->query($query_history);

if (!$result_history) {
    die("Query gagal: " . $conn->error);
}

$orders = $result_history->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order List - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
       html {
            overflow-y: scroll;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            overflow-y: auto;
        }
        nav {
            background-color: #333;
            padding: 15px 0;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 1000;
        }
        nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            padding: 0;
            margin: 0;
        }
        nav ul li {
            margin: 0 20px;
        }
        nav a {
            color: white;
            text-decoration: none;
            font-size: 20px;
            font-weight: bold;
            padding: 10px 20px;
        }
        nav a:hover {
            color: #007bff;
            background-color: #444;
            border-radius: 5px;
        }
        .order-section {
            padding: 20px;
            margin: 60px auto;
            max-width: 1200px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .order-section h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #007bff;
            color: white;
        }
        .success-message, .error-message {
            margin: 20px 0;
            padding: 10px;
            border-radius: 5px;
            color: white;
        }
        .success-message {
            background-color: #28a745;
        }
        .error-message {
            background-color: #dc3545;
        }
    </style>
</head>
<body>

<nav>
    <ul>
    <li><a href="admin_dashboard.php">Home</a></li>
        <li><a href="review.php">Review</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="product.php">Product</a></li>
        <li><a href="order.php">Order</a></li>
        <li><a href="financial.php">Financial Report</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<section class="order-section">
    <h2>Order List</h2>
    <?php if (count($orders) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pelanggan</th>
                    <th>Nama Produk</th>
                    <th>Material</th>
                    <th>Warna</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Tanggal Order</th>
                    <th>Estimasi Selesai</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $index => $order): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($order['Nama_Pelanggan_History']); ?></td>
                        <td><?php echo htmlspecialchars($order['Nama_Produk_History']); ?></td>
                        <td><?php echo htmlspecialchars($order['Material_Produk_History']); ?></td>
                        <td><?php echo htmlspecialchars($order['Warna_Produk_History']); ?></td>
                        <td>Rp <?php echo number_format($order['Harga_Produk_History'], 0, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($order['Jumlah_Produk_History']); ?></td>
                        <td><?php echo htmlspecialchars($order['Tanggal_History']); ?></td>
                        <td><?php echo htmlspecialchars($order['Tanggal_Estimasi']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="error-message">Belum ada order yang tercatat.</p>
    <?php endif; ?>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function () {
            const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
            if (scrollbarWidth > 0) {
                document.body.style.paddingRight = `${scrollbarWidth}px`;
            }
        });
</script>
</body>
</html>
