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

// Proses form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $review_contact = $_POST['review_contact'];
    $date_contact = date("Y-m-d H:i:s"); // Format waktu saat ini (YYYY-MM-DD HH:MM:SS)

    // Masukkan nama pelanggan, review, dan waktu submit ke database
    $query_insert = "INSERT INTO contact (Nama_Contact, Review_Contact, Date_Contact) 
                     VALUES ('$nama_pelanggan', '$review_contact', '$date_contact')";
    if ($conn->query($query_insert) === TRUE) {
        echo "<script>
                alert('Submit berhasil!');
                window.location.href='contact.php';
              </script>";
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
    <title>Kontak</title>
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
        .contact-form {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .contact-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .contact-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .contact-form button:hover {
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

<div class="contact-form">
    <h2>Form Kontak</h2>
    <p><strong>Nama:</strong> <?php echo htmlspecialchars($nama_pelanggan); ?></p>
    <form action="contact.php" method="POST">
        <label for="review_contact"><p><strong>Pesan :</strong></p></label>
        <textarea name="review_contact" id="review_contact" rows="4" required></textarea>
        <p><button type="submit">Kirim</button></p>
    </form>
</div>

</body>
</html>
