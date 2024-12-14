<?php
// Memulai sesi
session_start();
require_once('../config/db_connection.php');

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ambil data pelanggan berdasarkan sesi login
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


// Query untuk mengambil data review dari tabel 'review'
$query_reviews = "SELECT Nama_Pelanggan_Review, Nama_Produk_Review, Komentar_Review, `Nilai Penilaian` FROM review";
$result = $conn->query($query_reviews);

if (!$result) {
    die("Query gagal: " . $conn->error);
}

$reviews = $result->fetch_all(MYSQLI_ASSOC);

$nilai_mapping = [
    1 => 'Sangat Buruk',
    2 => 'Buruk',
    3 => 'Cukup',
    4 => 'Baik',
    5 => 'Sangat Baik'
];

// Proses pengiriman review
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_produk = $_POST['nama_produk'];
    $komentar = $_POST['komentar'];
    $nilai_penilaian = $_POST['nilai_penilaian'];

    $query_insert = "INSERT INTO review (Nama_Pelanggan_Review, Nama_Produk_Review, Komentar_Review, `Nilai Penilaian`) 
                     VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query_insert);
    $stmt->bind_param("sssi", $nama_pelanggan, $nama_produk, $komentar, $nilai_penilaian);

    if ($stmt->execute()) {
        // Redirect setelah berhasil
        header("Location: review.php?message=Review berhasil ditambahkan");
        exit();
    } else {
        die("Error: " . $conn->error);
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
            margin: 0 20px;  /* Menambah jarak antar item */
        }

        nav a {
            color: white;
            text-decoration: none;
            font-size: 20px;  /* Memperbesar ukuran font */
            font-weight: bold;  /* Menambah ketebalan teks */
            padding: 10px 20px;  /* Memberikan padding untuk memperbesar area klik */
        }

        nav a:hover {
            color: #007bff;
            background-color: #444; /* Menambahkan efek background saat hover */
            border-radius: 5px; /* Memberikan efek melengkung pada sudut */
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
        .review-section {
            padding: 20px;
        }
        .review-section h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .add-review-button {
            display: block;
            margin: 0 auto 20px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }
        .add-review-button:hover {
            background-color: #218838;
        }
        .review-list {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 20px;
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
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            padding: 50px;
            border-radius: 8px;
            width: 400px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }
        .modal-content form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .modal-content label {
            font-weight: bold;
        }
        .modal-content input, .modal-content textarea, .modal-content select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .modal-content textarea {
            resize: none;
        }
        .modal-content button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .modal-content button:hover {
            background-color: #0056b3;
        }
        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
        }
        .back-button {
            background-color: #6c757d;
            color: white;
            padding: 10px;
            text-align: center;
            border: none;
            border-radius: 5px;
            margin-top: -10px;
        }
        .back-button:hover {
            background-color: #5a6268;
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

    <section class="review-section">
    <h2>Review Penilaian Barang</h2>
    <div class="review-list">
        <?php foreach ($reviews as $review): ?>
            <div class="review-item">
                <h3><?php echo htmlspecialchars($review['Nama_Produk_Review']); ?></h3>
                <p><strong><?php echo htmlspecialchars($review['Nama_Pelanggan_Review']); ?></strong></p>
                <p><?php echo htmlspecialchars($review['Komentar_Review']); ?></p>
                <p>
                    <strong>Nilai:</strong> 
                    <?php 
                        $nilai = $review['Nilai Penilaian'];
                        echo $nilai ? "$nilai - " . $nilai_mapping[$nilai] : 'Belum diberi';
                    ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
</script>
</body>
</html>

