<?php 
// Memulai sesi
session_start();

// Memuat koneksi database
require_once('../config/db_connection.php');

// Query untuk mengambil data review dari tabel 'review'
$query_reviews = "SELECT Nama_Pelanggan_Review, Nama_Produk_Review, Komentar_Review FROM review";
$result = $conn->query($query_reviews);

// Periksa apakah ada kesalahan pada query
if (!$result) {
    die("Query gagal: " . $conn->error);
}

// Ambil data review dalam bentuk array
$reviews = $result->fetch_all(MYSQLI_ASSOC);

// Proses pengiriman review
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $nama_produk = $_POST['nama_produk'];
    $komentar = $_POST['komentar'];

    // Query untuk menyimpan review ke database
    $query_insert = "INSERT INTO review (Nama_Pelanggan_Review, Nama_Produk_Review, Komentar_Review) 
                     VALUES ('$nama_pelanggan', '$nama_produk', '$komentar')";
    if ($conn->query($query_insert) === TRUE) {
        // Jika berhasil, redirect atau tampilkan pesan
        echo "Review berhasil ditambahkan!";
        // Reload halaman untuk menampilkan review yang baru ditambahkan
        header("Location: review.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Barang</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* CSS dari file HTML sebelumnya */
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
        
        .review-section {
            padding: 20px;
        }

        .review-section h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Updated Review List to display 3 items per row */
        .review-list {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* Force 3 items per row */
            gap: 20px;
            padding: 20px;
            /* Ensure that items wrap properly */
            width: 100%;
        }

        .review-item {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .review-item h3 {
            font-size: 18px;
            margin: 10px 0;
        }

        .review-item p {
            font-size: 14px;
            color: #555;
        }

        /* Form styling */
        .review-form {
            margin-top: 30px;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .review-form input, .review-form textarea {
            width: 100%;
            padding: 5px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .review-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .review-form button:hover {
            background-color: #0056b3;
        }
        .cart-icon {
            position: absolute;
            right: 20px; /* Menempatkan ikon di kanan */
            font-size: 24px;
            color: white;
        }

        .cart-icon:hover {
            color: #3498db;
        }

        /* Responsive layout for smaller screens */
        @media (max-width: 900px) {
            .review-list {
                grid-template-columns: repeat(2, 1fr); /* 2 items per row for medium screens */
            }
        }

        @media (max-width: 600px) {
            .review-list {
                grid-template-columns: 1fr; /* 1 item per row for small screens */
            }
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
        <li><a href="logout.php">Logout</a></li>
        <li><a href="cart.php"><i class="fas fa-shopping-cart cart-icon"></i></a></li>
    </ul>
</nav>

<section class="review-section">
    <h2>Review Penilaian Barang</h2>
    <div class="review-list">
        <?php foreach ($reviews as $review): ?>
            <div class="review-item">
                <h3><?php echo htmlspecialchars($review['Nama_Produk_Review']); ?></h3>
                <p><strong><?php echo htmlspecialchars($review['Nama_Pelanggan_Review']); ?></strong></p>
                <p><?php echo htmlspecialchars($review['Komentar_Review']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Form Tambah Review -->
    <div class="review-form">
        <h3>Tambah Review</h3>
        <form action="review.php" method="POST">
            <input type="text" name="nama_pelanggan" placeholder="Nama Pelanggan" required>
            <input type="text" name="nama_produk" placeholder="Nama Produk" required>
            <textarea name="komentar" placeholder="Komentar Review" rows="4" required></textarea>
            <button type="submit">Kirim Review</button>
        </form>
    </div>
</section>

</body>
</html>
