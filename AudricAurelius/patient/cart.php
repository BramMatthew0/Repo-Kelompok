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

// Hapus item dari keranjang jika parameter 'remove' ada
if (isset($_GET['remove'])) {
    $id_keranjang = intval($_GET['remove']); // Pastikan nilai ID valid
    $query_remove = "DELETE FROM keranjang WHERE Id_Produk_Keranjang = ? AND Nama_Pelanggan_Keranjang = ?";
    $stmt = $conn->prepare($query_remove);
    $stmt->bind_param("is", $id_keranjang, $nama_pelanggan);
    if ($stmt->execute()) {
        header("Location: cart.php?message=Item%20berhasil%20dihapus");
        exit();
    } else {
        die("Gagal menghapus item: " . $conn->error);
    }
}

// Handle Checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    // Query untuk memindahkan data dari keranjang ke history
    $query_move_to_history = "INSERT INTO history (Nama_Pelanggan_History, Id_Produk_History, Nama_Produk_History, Material_Produk_History, Warna_Produk_History, Harga_Produk_History, Jumlah_Produk_History)
                              SELECT Nama_Pelanggan_Keranjang, Id_Produk_Keranjang, Nama_Produk_Keranjang, Material_Produk_Keranjang, Warna_Produk_Keranjang, Harga_Produk_Keranjang, Jumlah_Produk_Keranjang
                              FROM keranjang
                              WHERE Nama_Pelanggan_Keranjang = ?";
    $stmt = $conn->prepare($query_move_to_history);
    $stmt->bind_param("s", $nama_pelanggan);

    if ($stmt->execute()) {
        // Mengambil data keranjang untuk mengurangi stok produk
        $query_cart_items = "SELECT Id_Produk_Keranjang, Jumlah_Produk_Keranjang FROM keranjang WHERE Nama_Pelanggan_Keranjang = ?";
        $stmt_cart_items = $conn->prepare($query_cart_items);
        $stmt_cart_items->bind_param("s", $nama_pelanggan);
        $stmt_cart_items->execute();
        $result_cart_items = $stmt_cart_items->get_result();

        // Kurangi stok produk berdasarkan data di keranjang
        while ($cart_item = $result_cart_items->fetch_assoc()) {
            $id_produk = $cart_item['Id_Produk_Keranjang'];
            $jumlah_pesanan = (int)$cart_item['Jumlah_Produk_Keranjang'];

            // Kurangi stok di tabel produk
            $query_update_stok = "UPDATE produk SET Stok_Produk = Stok_Produk - ? WHERE Id_Produk = ? AND Stok_Produk >= ?";
            $stmt_update_stok = $conn->prepare($query_update_stok);
            $stmt_update_stok->bind_param("iii", $jumlah_pesanan, $id_produk, $jumlah_pesanan);
            if (!$stmt_update_stok->execute()) {
                die("Gagal mengupdate stok produk: " . $conn->error);
            }
        }

        // Jika berhasil, kosongkan tabel keranjang untuk pelanggan
        $query_clear_cart = "DELETE FROM keranjang WHERE Nama_Pelanggan_Keranjang = ?";
        $stmt_clear = $conn->prepare($query_clear_cart);
        $stmt_clear->bind_param("s", $nama_pelanggan);

        if ($stmt_clear->execute()) {
            // Redirect dengan pesan sukses
            header("Location: cart.php?message=Checkout%20berhasil");
            exit();
        } else {
            die("Gagal mengosongkan keranjang: " . $conn->error);
        }
    } else {
        die("Gagal memindahkan data ke history: " . $conn->error);
    }
}

// Ambil pesan sukses dari parameter URL jika ada
$success_message = isset($_GET['message']) ? $_GET['message'] : null;

// Mengambil data keranjang berdasarkan pelanggan yang login
$query = "SELECT Id_Produk_Keranjang AS ID_Keranjang, Nama_Produk_Keranjang, Material_Produk_Keranjang, Warna_Produk_Keranjang, Harga_Produk_Keranjang, Jumlah_Produk_Keranjang 
          FROM keranjang 
          WHERE Nama_Pelanggan_Keranjang = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $nama_pelanggan);
$stmt->execute();
$result = $stmt->get_result();

$message = null;
if ($result->num_rows == 0) {
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
        .cart-items {
            padding: 20px;
            margin: 20px auto;
            max-width: 800px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #ccc;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .action-buttons a {
            text-decoration: none;
            padding: 10px 15px;
            color: white;
            border-radius: 5px;
        }
        .action-buttons .edit {
            background-color: #3498db;
        }
        .action-buttons .edit:hover {
            background-color: #2980b9;
        }
        .action-buttons .remove {
            background-color: #e74c3c;
        }
        .action-buttons .remove:hover {
            background-color: #c0392b;
        }
        .checkout-button {
            display: block;
            text-align: center;
            margin: 20px auto 0;
            padding: 10px 20px;
            background-color: #2ecc71;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .checkout-button:hover {
            background-color: #27ae60;
        }
        .success-message {
            color: green;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
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
            <li><a href="product.php">Product</a></li>
            <li><a href="history.php">History</a></li>
            <li><a href="logout.php">Logout</a></li>
            <li><a href="cart.php"><i class="fas fa-shopping-cart cart-icon"></i></a></li>
        </ul>
    </nav>

    <!-- Keranjang Belanja -->
    <section class="cart-items">
        <h2>Keranjang Belanja Anda</h2>

        <!-- Tampilkan pesan sukses jika ada -->
        <?php if ($success_message): ?>
            <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>

        <?php if ($message): ?>
            <p><?php echo $message; ?></p>
        <?php else: ?>
            <?php while ($item = $result->fetch_assoc()): ?>
                <div class="cart-item">
                    <div>
                        <p><strong><?php echo htmlspecialchars($item['Nama_Produk_Keranjang']); ?></strong></p>
                        <p>Material: <?php echo htmlspecialchars($item['Material_Produk_Keranjang']); ?></p>
                        <p>Warna: <?php echo htmlspecialchars($item['Warna_Produk_Keranjang']); ?></p>
                        <p>Harga: Rp <?php echo number_format($item['Harga_Produk_Keranjang'], 0, ',', '.'); ?></p>
                        <p>Jumlah: <?php echo (int)$item['Jumlah_Produk_Keranjang']; ?></p>
                    </div>
                    <div class="action-buttons">
                        <a class="edit" href="edit.php?id=<?php echo $item['ID_Keranjang']; ?>">Edit</a>
                        <a class="remove" href="cart.php?remove=<?php echo $item['ID_Keranjang']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus item ini?')">Remove</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>

        <form method="POST" action="">
            <button type="submit" name="checkout" class="checkout-button">Checkout</button>
        </form>
    </section>
</body>
</html>
