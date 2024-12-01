<?php
session_start();
require_once('../config/db_connection.php');

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ambil data pelanggan yang sedang login
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

// Periksa apakah parameter ID Produk ada
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID Produk tidak valid.");
}

$id_produk = intval($_GET['id']);

// Ambil data produk dari keranjang berdasarkan ID dan pelanggan
$query = "SELECT Nama_Produk_Keranjang, Material_Produk_Keranjang, Warna_Produk_Keranjang, Jumlah_Produk_Keranjang 
          FROM keranjang 
          WHERE Id_Produk_Keranjang = ? AND Nama_Pelanggan_Keranjang = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $id_produk, $nama_pelanggan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Produk tidak ditemukan di keranjang Anda.");
}

$produk = $result->fetch_assoc();

// Proses update material, warna, dan jumlah produk
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_material = $_POST['material'];
    $new_warna = $_POST['warna'];
    $new_jumlah = intval($_POST['jumlah']);

    if ($new_jumlah < 1) {
        die("Jumlah produk harus minimal 1.");
    }

    $query_update = "UPDATE keranjang 
                     SET Material_Produk_Keranjang = ?, Warna_Produk_Keranjang = ?, Jumlah_Produk_Keranjang = ? 
                     WHERE Id_Produk_Keranjang = ? AND Nama_Pelanggan_Keranjang = ?";
    $stmt_update = $conn->prepare($query_update);
    $stmt_update->bind_param("ssiis", $new_material, $new_warna, $new_jumlah, $id_produk, $nama_pelanggan);

    if ($stmt_update->execute()) {
        header("Location: cart.php?message=Item%20berhasil%20diedit");
        exit();
    } else {
        die("Gagal mengupdate produk: " . $conn->error);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .edit-section {
            padding: 20px;
            margin: 20px auto;
            max-width: 500px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .edit-section h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .edit-form {
            display: flex;
            flex-direction: column;
        }
        .edit-form label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        .edit-form select, .edit-form input {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .edit-form button {
            padding: 10px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .edit-form button:hover {
            background-color: #2980b9;
        }
        .back-button {
            display: block;
            margin: 10px auto;
            text-align: center;
            padding: 10px;
            background-color: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            width: 100px;
        }
        .back-button:hover {
            background-color: #c0392b;
        }
        
    </style>
</head>
<body>
<section class="edit-section">
    <h2>Edit Produk</h2>
    <p><strong>Produk:</strong> <?php echo htmlspecialchars($produk['Nama_Produk_Keranjang']); ?></p>
    <form class="edit-form" method="POST" action="">
        <label for="material">Material:</label>
        <select id="material" name="material" required>
            <?php
            $materials = ['Kayu', 'Kaca', 'Plastik', 'Logam'];
            foreach ($materials as $material) {
                $selected = ($material === $produk['Material_Produk_Keranjang']) ? 'selected' : '';
                echo "<option value=\"$material\" $selected>$material</option>";
            }
            ?>
        </select>

        <label for="warna">Warna:</label>
        <select id="warna" name="warna" required>
            <?php
            $colors = ['Merah', 'Kuning', 'Hijau', 'Biru', 'Ungu', 'Hitam', 'Putih', 'Abu', 'Coklat'];
            foreach ($colors as $color) {
                $selected = ($color === $produk['Warna_Produk_Keranjang']) ? 'selected' : '';
                echo "<option value=\"$color\" $selected>$color</option>";
            }
            ?>
        </select>

        <label for="jumlah">Jumlah:</label>
        <input type="number" id="jumlah" name="jumlah" value="<?php echo htmlspecialchars($produk['Jumlah_Produk_Keranjang']); ?>" required>

        <button type="submit">Update</button>
    </form>
    <a class="back-button" href="cart.php">Kembali</a>
</section>
</body>
</html>
