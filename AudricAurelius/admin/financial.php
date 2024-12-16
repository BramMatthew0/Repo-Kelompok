<?php
session_start();
require_once('../config/db_connection.php');

// Pastikan user adalah admin
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Query untuk laporan keuangan per bulan dan per furnitur
$query = "
    SELECT 
        DATE_FORMAT(Tanggal_History, '%Y-%m') AS Bulan,
        Nama_Produk_History AS Produk,
        SUM(Harga_Produk_History * Jumlah_Produk_History) AS Total
    FROM history
    GROUP BY Bulan, Produk
    ORDER BY Bulan DESC, Total DESC";
$result = $conn->query($query);

// Mengelompokkan hasil query berdasarkan bulan
$report = [];
while ($row = $result->fetch_assoc()) {
    $report[$row['Bulan']][] = [
        'Produk' => $row['Produk'],
        'Total' => $row['Total']
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Report</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        nav {
            background-color: #333;
            padding: 15px 0;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
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
        .report-section {
            padding: 20px;
            margin: 20px auto;
            max-width: 1200px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .report-section h2 {
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
        .month-title {
            background-color: #f0f0f0;
            font-weight: bold;
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

<section class="report-section">
    <h2>Laporan Keuangan</h2>

    <?php foreach ($report as $month => $products): ?>
        <h3 class="month-title">Bulan: <?php echo htmlspecialchars($month); ?></h3>
        <table>
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Total Pendapatan (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['Produk']); ?></td>
                        <td>Rp <?php echo number_format($product['Total'], 0, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
</section>

</body>
</html>