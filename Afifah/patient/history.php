<?php
// Memulai sesi
session_start();
require_once('../config/db_connection.php');

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ambil data nama pelanggan berdasarkan sesi login
$username = $_SESSION['username'];
$query_pelanggan = "SELECT Nama FROM pelanggan WHERE Username = ?";
$stmt = $conn->prepare($query_pelanggan);
$stmt->bind_param("s", $username);
$stmt->execute();
$result_pelanggan = $stmt->get_result();

if (!$result_pelanggan) {
    die("Query pelanggan gagal: " . $conn->error);
}

$pelanggan = $result_pelanggan->fetch_assoc();
$nama_pelanggan = $pelanggan['Nama'];

// Query untuk mengambil data history pemesanan
$query_history = "SELECT Id_Produk_History, Tanggal_History, Tanggal_Estimasi, Nama_Produk_History, Material_Produk_History, Warna_Produk_History, 
                  Jumlah_Produk_History, (Harga_Produk_History * Jumlah_Produk_History) AS Total_Harga 
                  FROM history 
                  WHERE Nama_Pelanggan_History = ?
                  ORDER BY Tanggal_History DESC, Nama_Produk_History ASC";
$stmt_history = $conn->prepare($query_history);
$stmt_history->bind_param("s", $nama_pelanggan);
$stmt_history->execute();
$result_history = $stmt_history->get_result();

if (!$result_history) {
    die("Query history gagal: " . $conn->error);
}

// Fetch data history
$history_items = $result_history->fetch_all(MYSQLI_ASSOC);

// Proses untuk menambahkan tanggal estimasi ke database jika belum ada
foreach ($history_items as $key => $item) {
    if (empty($item['Tanggal_Estimasi'])) {
        $tanggal_history = new DateTime($item['Tanggal_History']);
        $random_days = rand(1, 14); // Random antara 1 hingga 14 hari
        $tanggal_estimasi = clone $tanggal_history;
        $tanggal_estimasi->modify("+$random_days days");
        $tanggal_estimasi_str = $tanggal_estimasi->format('Y-m-d');

        // Update tanggal estimasi ke database
        $query_update = "UPDATE history SET Tanggal_Estimasi = ? WHERE Id_Produk_History = ?";
        $stmt_update = $conn->prepare($query_update);
        $stmt_update->bind_param("si", $tanggal_estimasi_str, $item['Id_Produk_History']);
        $stmt_update->execute();

        // Update nilai di array history_items agar konsisten dengan database
        $history_items[$key]['Tanggal_Estimasi'] = $tanggal_estimasi_str;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Pemesanan</title>
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
            position: relative;
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
        .history-section {
            padding: 20px;
            max-width: 1000px;
            margin: 20px auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .history-section h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .history-table {
            width: 100%;
            border-collapse: collapse;
        }
        .history-table th, .history-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .history-table th {
            background-color: #007bff;
            color: white;
        }
        .history-table tr:nth-child(even) {
            background-color: #f4f4f4;
        }
        .history-table tr:hover {
            background-color: #ddd;
        }
        .history-table td {
            vertical-align: top;
        }
        .cart-icon {
            position: absolute;
            right: 20px;
            font-size: 24px;
            color: white;
        }
        .cart-icon:hover {
            color: #3498db;
        }
    </style>
</head>
<body>
<nav>
    <ul>
        <li><a href="pelanggan_dashboard.php">Home</a></li>
        <li><a href="review.php">Review</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="product.php">Product</a></li>
        <li><a href="history.php">History</a></li>
        <li><a href="logout.php">Logout</a></li>
        <li><a href="cart.php"><i class="fas fa-shopping-cart cart-icon"></i></a></li>
    </ul>
</nav>

<section class="history-section">
    <h2>History Pemesanan</h2>
    <?php if (count($history_items) > 0): ?>
        <table class="history-table">
            <thead>
                <tr>
                    <th>Tanggal Pembelian</th>
                    <th>Estimasi Selesai</th>
                    <th>Produk</th>
                    <th>Material</th>
                    <th>Warna</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $current_date = null;
                foreach ($history_items as $index => $item): ?>
                    <tr>
                        <!-- Tampilkan tanggal hanya sekali untuk setiap grup -->
                        <?php if ($current_date !== $item['Tanggal_History']): ?>
                            <td rowspan="<?php echo count(array_filter($history_items, function($i) use ($item) { return $i['Tanggal_History'] === $item['Tanggal_History']; })); ?>" style="text-align: center;">
                                <?php echo htmlspecialchars($item['Tanggal_History']); ?>
                            </td>
                            <td rowspan="<?php echo count(array_filter($history_items, function($i) use ($item) { return $i['Tanggal_History'] === $item['Tanggal_History']; })); ?>" style="text-align: center;">
                                <?php echo htmlspecialchars($item['Tanggal_Estimasi']); ?>
                            </td>
                            <?php $current_date = $item['Tanggal_History']; ?>
                        <?php endif; ?>
                        <td><?php echo htmlspecialchars($item['Nama_Produk_History']); ?></td>
                        <td><?php echo htmlspecialchars($item['Material_Produk_History']); ?></td>
                        <td><?php echo htmlspecialchars($item['Warna_Produk_History']); ?></td>
                        <td style="text-align: center;"><?php echo (int)$item['Jumlah_Produk_History']; ?></td>
                        <td style="text-align: right;">Rp <?php echo number_format($item['Total_Harga'], 0, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align: center; color: #555;">Belum ada history pemesanan.</p>
    <?php endif; ?>
</section>

</body>
</html>
