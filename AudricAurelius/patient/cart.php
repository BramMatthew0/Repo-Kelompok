<?php 
session_start();
require_once('../config/db_connection.php');

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ambil data nama pelanggan dari database berdasarkan username di sesi
$username = $_SESSION['username'];
$query_pelanggan = "SELECT Nama FROM pelanggan WHERE Username = '$username'";
$result_pelanggan = $conn->query($query_pelanggan);

if (!$result_pelanggan) {
    die("Query pelanggan gagal: " . $conn->error);
}

$pelanggan = $result_pelanggan->fetch_assoc();
$nama_pelanggan = $pelanggan['Nama'];

// Mengambil data keranjang berdasarkan pelanggan yang login
$query = "SELECT * FROM keranjang WHERE Nama_Pelanggan_Keranjang = '$nama_pelanggan'";
$result = mysqli_query($conn, $query);

// Menampilkan pesan jika keranjang kosong
if (mysqli_num_rows($result) == 0) {
    $message = "Keranjang Anda kosong.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Style untuk halaman keranjang */
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
        .cart-items {
            padding: 20px;
            background-color: white;
            margin: 20px;
        }
        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: #fff;
            margin-bottom: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 15px;
        }
        .cart-item p {
            margin: 0;
            font-size: 16px;
        }
        .cart-item .price {
            font-weight: bold;
            color: #e74c3c;
        }
        .checkout-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .checkout-button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav>
        <ul>
            <li><a href="pelanggan_dashboard.php">Home</a></li>
            <li><a href="review.php">Review</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="catalogue.php">Product</a></li>
            <li><a href="logout.php">Logout</a></li>
            <li><a href="cart.php"><i class="fas fa-shopping-cart cart-icon"></i></a></li>
        </ul>
    </nav>

    <!-- Keranjang Belanja -->
    <section class="cart-items">
        <h2>Keranjang Belanja Anda</h2>

        <?php if (isset($message)): ?>
            <p><?php echo $message; ?></p>
        <?php else: ?>
            <?php while ($item = mysqli_fetch_assoc($result)): ?>
                <div class="cart-item">
                    <div class="cart-item-details">
                        <p><?php echo $item['Nama_Produk_Keranjang']; ?></p>
                        <p class="price">Rp <?php echo number_format($item['Harga_Produk_Keranjang'], 0, ',', '.'); ?></p>
                        <p>Jumlah: <?php echo $item['Jumlah_Produk_Keranjang']; ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>

        <a href="checkout.php" class="checkout-button">Proses Pembayaran</a>
    </section>
</body>
</html>
