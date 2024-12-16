<?php
session_start();
require_once('../config/db_connection.php');

// Pastikan user adalah admin
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil data nama admin dari sesi
$admin_name = "Admin"; // Nama pengirim selalu Admin

// Proses pengiriman pesan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $review_contact = trim($_POST['review_contact']);
    $date_contact = date("Y-m-d H:i:s"); // Format waktu saat ini (YYYY-MM-DD HH:MM:SS)
    $pengirim = $admin_name; // Nama pengirim selalu Admin
    $penerima = "All"; // Bisa diatur ke semua penerima atau spesifik

    if (!empty($review_contact)) {
        // Masukkan data pesan ke database
        $query_insert = "INSERT INTO contact (Nama_Contact, Review_Contact, Date_Contact, Penerima) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($query_insert);

        if ($stmt_insert) {
            $stmt_insert->bind_param("ssss", $pengirim, $review_contact, $date_contact, $penerima);
            if ($stmt_insert->execute()) {
                echo "<script>
                        alert('Pesan berhasil dikirim!');
                        window.location.href='contact.php';
                      </script>";
                exit();
            } else {
                echo "Kesalahan saat menyimpan pesan: " . $conn->error;
            }
            $stmt_insert->close();
        } else {
            die("Kesalahan pada query insert: " . $conn->error);
        }
    } else {
        echo "<script>alert('Pesan tidak boleh kosong.');</script>";
    }
}

// Ambil semua data dari tabel contact
$query_contacts = "SELECT Nama_Contact, Review_Contact, Date_Contact 
                   FROM contact 
                   ORDER BY Date_Contact DESC";
$stmt_contacts = $conn->prepare($query_contacts);

if ($stmt_contacts) {
    $stmt_contacts->execute();
    $result_contacts = $stmt_contacts->get_result();
} else {
    die("Query kontak gagal: " . $conn->error);
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

        .contact-table {
            max-width: 800px;
            margin: 50px auto;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .contact-table h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
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

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
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

<div class="contact-form">
    <h2>Form Kontak</h2>
    <p><strong>Nama:</strong> Admin</p>
    <form action="contact.php" method="POST">
        <label for="review_contact"><p><strong>Pesan :</strong></p></label>
        <textarea name="review_contact" id="review_contact" rows="4" required></textarea>
        <p><button type="submit">Kirim</button></p>
    </form>
</div>

<div class="contact-table">
    <h2>Daftar Kontak</h2>
    <?php if ($result_contacts->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Pesan</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_contacts->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['Nama_Contact']); ?></td>
                        <td><?php echo htmlspecialchars($row['Review_Contact']); ?></td>
                        <td><?php echo htmlspecialchars($row['Date_Contact']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align: center;">Tidak ada kontak yang tersedia.</p>
    <?php endif; ?>
</div>
</body>
</html>
