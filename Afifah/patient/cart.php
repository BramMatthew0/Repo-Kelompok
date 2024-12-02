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

// Tangani pembaruan data keranjang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    $id_keranjang = intval($_POST['id_keranjang']);
    $material = $_POST['material'];
    $warna = $_POST['warna'];
    $jumlah = intval($_POST['jumlah']);

    // Validasi input
    if ($id_keranjang > 0 && $jumlah > 0 && !empty($material) && !empty($warna)) {
        $query_update = "UPDATE keranjang 
                         SET Material_Produk_Keranjang = ?, 
                             Warna_Produk_Keranjang = ?, 
                             Jumlah_Produk_Keranjang = ? 
                         WHERE Id_Produk_Keranjang = ?";
        $stmt = $conn->prepare($query_update);
        $stmt->bind_param("ssii", $material, $warna, $jumlah, $id_keranjang);

        if ($stmt->execute()) {
            header("Location: cart.php?message=Item%20berhasil%20diperbarui");
            exit();
        } else {
            die("Gagal memperbarui item: " . $stmt->error);
        }
    } else {
        die("Data tidak valid. Pastikan semua field diisi dengan benar.");
    }
}

// Hapus item dari keranjang jika parameter 'remove' ada
if (isset($_GET['remove'])) {
    $id_keranjang = intval($_GET['remove']); 
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

// Checkout logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $query_cart = "SELECT * FROM keranjang WHERE Nama_Pelanggan_Keranjang = ?";
    $stmt_cart = $conn->prepare($query_cart);
    $stmt_cart->bind_param("s", $nama_pelanggan);
    $stmt_cart->execute();
    $result_cart = $stmt_cart->get_result();

    if ($result_cart->num_rows > 0) {
        $conn->begin_transaction();

        try {
            while ($row = $result_cart->fetch_assoc()) {
                // Pindahkan data ke tabel history
                $query_insert_history = "INSERT INTO history 
                    (Nama_Pelanggan_History, Id_Produk_History, Nama_Produk_History, Material_Produk_History, Warna_Produk_History, Harga_Produk_History, Jumlah_Produk_History, Tanggal_History, Tanggal_Estimasi)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt_history = $conn->prepare($query_insert_history);
                $tanggal = date("Y-m-d");
                $tanggal_estimasi = date("Y-m-d", strtotime("+3 days"));
                $stmt_history->bind_param(
                    "sissssiss",
                    $row['Nama_Pelanggan_Keranjang'],
                    $row['Id_Produk_Keranjang'],
                    $row['Nama_Produk_Keranjang'],
                    $row['Material_Produk_Keranjang'],
                    $row['Warna_Produk_Keranjang'],
                    $row['Harga_Produk_Keranjang'],
                    $row['Jumlah_Produk_Keranjang'],
                    $tanggal,
                    $tanggal_estimasi
                );
                $stmt_history->execute();

                // Kurangi stok produk
                $query_update_stock = "UPDATE produk 
                                       SET Stok_Produk = Stok_Produk - ? 
                                       WHERE Id_Produk = ? AND Stok_Produk >= ?";
                $stmt_stock = $conn->prepare($query_update_stock);
                $stmt_stock->bind_param(
                    "iii",
                    $row['Jumlah_Produk_Keranjang'],
                    $row['Id_Produk_Keranjang'],
                    $row['Jumlah_Produk_Keranjang']
                );
                $stmt_stock->execute();

                if ($stmt_stock->affected_rows === 0) {
                    throw new Exception("Stok tidak mencukupi untuk produk: " . $row['Nama_Produk_Keranjang']);
                }
            }

            // Kosongkan keranjang
            $query_delete_cart = "DELETE FROM keranjang WHERE Nama_Pelanggan_Keranjang = ?";
            $stmt_delete = $conn->prepare($query_delete_cart);
            $stmt_delete->bind_param("s", $nama_pelanggan);
            $stmt_delete->execute();

            $conn->commit();

            header("Location: cart.php?message=Checkout%20berhasil");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            die("Terjadi kesalahan saat proses checkout: " . $e->getMessage());
        }
    } else {
        header("Location: cart.php?message=Keranjang%20kosong");
        exit();
    }
}

// Ambil data keranjang
$query = "SELECT Id_Produk_Keranjang AS ID_Keranjang, Nama_Produk_Keranjang, Material_Produk_Keranjang, Warna_Produk_Keranjang, Harga_Produk_Keranjang, Jumlah_Produk_Keranjang 
          FROM keranjang 
          WHERE Nama_Pelanggan_Keranjang = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $nama_pelanggan);
$stmt->execute();
$result = $stmt->get_result();

// Hitung total harga
$total_harga = 0;
while ($item = $result->fetch_assoc()) {
    $total_harga += $item['Harga_Produk_Keranjang'] * $item['Jumlah_Produk_Keranjang'];
}
$result->data_seek(0);

// Ambil pesan sukses dari parameter URL jika ada
$success_message = isset($_GET['message']) ? $_GET['message'] : null;
$message = $result->num_rows == 0 ? "Keranjang Anda kosong." : null;
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
            padding: 30px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }
        .modal-content form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .modal-content input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
        }
        .modal-content button {
            padding: 15px;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        .modal-content button:hover {
            opacity: 0.9;
        }
        select {
            font-size: 16px; /* Ukuran teks lebih besar */
            padding: 10px;   /* Menambah ruang di dalam dropdown */
            width: 100%;     /* Membuat lebar dropdown menyesuaikan kontainer */
            border: 1px solid #ccc; /* Border untuk tampilan lebih jelas */
            border-radius: 5px; /* Membuat sudut dropdown melengkung */
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1); /* Memberikan efek bayangan */
            background-color: #fff; /* Warna latar belakang dropdown */
            cursor: pointer; /* Menambahkan pointer saat hover */
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
                        <a class="edit" href="javascript:void(0)" onclick="openEditModal('<?php echo $item['ID_Keranjang']; ?>', '<?php echo htmlspecialchars($item['Material_Produk_Keranjang']); ?>', '<?php echo htmlspecialchars($item['Warna_Produk_Keranjang']); ?>', '<?php echo (int)$item['Jumlah_Produk_Keranjang']; ?>')">Edit</a>
                        <a class="remove" href="cart.php?remove=<?php echo $item['ID_Keranjang']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus item ini?')">Remove</a>
                    </div>
                </div>
            <?php endwhile; ?>
            <?php if ($total_harga > 0): ?>
                <p style="font-weight: bold; font-size: 18px; text-align: center; margin-top: 20px;">
                    Total yang harus dibayarkan: Rp <?php echo number_format($total_harga, 0, ',', '.'); ?>
                </p>
            <?php endif; ?>
        <?php endif; ?>
        <form method="POST" action="">
            <button type="submit" name="checkout" class="checkout-button">Checkout</button>
        </form>
    </section>

    <!-- Modal Edit -->
    <div id="editModal" class="modal">
    <div class="modal-content">
        <h3>Edit Produk</h3>
        <form method="POST" action="cart.php">
            <input type="hidden" name="update_cart" value="1">
            <input type="hidden" id="editItemId" name="id_keranjang">
            
            <label for="editMaterial">Material:</label>
            <select id="editMaterial" name="material" required>
                <?php
                $materials = ['Kayu', 'Kaca', 'Plastik', 'Logam'];
                foreach ($materials as $material) {
                    echo "<option value=\"$material\">$material</option>";
                }
                ?>
            </select>

            <label for="editWarna">Warna:</label>
            <select id="editWarna" name="warna" required>
                <?php
                $colors = ['Merah', 'Kuning', 'Hijau', 'Biru', 'Ungu', 'Hitam', 'Putih', 'Abu', 'Coklat'];
                foreach ($colors as $color) {
                    echo "<option value=\"$color\">$color</option>";
                }
                ?>
            </select>

            <label for="editJumlah">Jumlah:</label>
            <input type="number" id="editJumlah" name="jumlah" min="1" required>

            <div style="display: flex; justify-content: space-between; gap: 10px; margin-top: 20px;">
                <button type="submit" style="background-color: #2ecc71; padding: 15px 25px; border: none; border-radius: 5px; color: white;">Update</button>
                <button type="button" style="background-color: #e74c3c; padding: 15px 25px; border: none; border-radius: 5px; color: white;" onclick="closeEditModal()">Back</button>
            </div>
        </form>
    </div>
</div>

    <!-- JavaScript -->
    <script>
        function openEditModal(id, material, warna, jumlah) {
            document.getElementById("editModal").style.display = "flex";
            document.getElementById("editItemId").value = id;
            document.getElementById("editMaterial").value = material;
            document.getElementById("editWarna").value = warna;
            document.getElementById("editJumlah").value = jumlah;
        }

        function closeEditModal() {
            document.getElementById("editModal").style.display = "none";
        }
    </script>
</body>
</html>
