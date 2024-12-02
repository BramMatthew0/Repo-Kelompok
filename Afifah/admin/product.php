<?php
session_start();
require_once('../config/db_connection.php');

// Pastikan user adalah admin
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Proses menambah produk baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $product_color = $_POST['product_color'];
    $product_price = $_POST['product_price'];
    $product_material = $_POST['product_material'];
    $product_stock = $_POST['product_stock'];

    // Insert produk baru ke database
    $query_add_product = "INSERT INTO produk (Nama_Produk, Warna_Produk, Harga_Produk, Material_Produk, Stok_Produk) 
                          VALUES (?, ?, ?, ?, ?)";
    $stmt_add_product = $conn->prepare($query_add_product);
    $stmt_add_product->bind_param("ssdsi", $product_name, $product_color, $product_price, $product_material, $product_stock);

    if ($stmt_add_product->execute()) {
        $success_message = "Produk baru berhasil ditambahkan!";
    } else {
        $error_message = "Gagal menambahkan produk baru: " . $conn->error;
    }
}

// Ambil semua produk untuk ditampilkan
$query_products = "SELECT * FROM produk";
$result = $conn->query($query_products);

if (!$result) {
    die("Query gagal: " . $conn->error);
}

$products = $result->fetch_all(MYSQLI_ASSOC);

// Fungsi kategori berdasarkan nama produk
function getCategory($productName) {
    $word = strtolower(explode(' ', $productName)[0]); // Get the first word
    switch ($word) {
        case 'lemari':
            return 'lemari';
        case 'kasur':
            return 'kasur';
        case 'meja':
            return 'meja';
        case 'kursi':
            return 'kursi';
        default:
            return 'komponen'; // Default for anything else
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Product Management</title>
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
        .product-section {
            padding: 20px;
        }
        .tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .tab {
            margin: 0 10px;
            padding: 10px 20px;
            background-color: #ddd;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .tab.active {
            background-color: #007bff;
            color: white;
        }
        .product-list {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .product-item {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .add-product-btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .add-product-btn:hover {
            background-color: #0056b3;
        }
        /* Modal Styling */
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
            background: white;
            padding: 50px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .modal-content h2 {
            margin-bottom: 20px;
        }
        .modal-content .form-group {
            margin-bottom: 15px;
        }
        .modal-content .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .modal-content .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .modal-content .btn {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .modal-content .btn:hover {
            background-color: #0056b3;
        }
        .modal-content .close-btn {
            float: right;
            cursor: pointer;
            font-size: 20px;
            color: red;
        }
    </style>
    <script>
        function toggleModal() {
            const modal = document.getElementById('addProductModal');
            modal.style.display = modal.style.display === 'flex' ? 'none' : 'flex';
        }
    </script>
        <script>
        function filterProducts(category) {
            const products = document.querySelectorAll('.product-item');
            products.forEach(product => {
                if (category === 'all' || product.dataset.category === category) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });

            // Update active tab
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            document.querySelector(`#tab-${category}`).classList.add('active');
        }
    </script>
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

<section class="product-section">
    <h2>Product List</h2>
    <div class="tabs">
        <div class="tab active" id="tab-all" onclick="filterProducts('all')">All</div>
        <div class="tab" id="tab-komponen" onclick="filterProducts('komponen')">Komponen</div>
        <div class="tab" id="tab-meja" onclick="filterProducts('meja')">Meja</div>
        <div class="tab" id="tab-kursi" onclick="filterProducts('kursi')">Kursi</div>
        <div class="tab" id="tab-lemari" onclick="filterProducts('lemari')">Lemari</div>
        <div class="tab" id="tab-kasur" onclick="filterProducts('kasur')">Kasur</div>
        <button class="add-product-btn" onclick="toggleModal()">Add New Product</button>
    </div>
    <div class="product-list">
        <?php foreach ($products as $product): ?>
            <div class="product-item" data-category="<?php echo htmlspecialchars(getCategory($product['Nama_Produk'])); ?>">
                <h3><?php echo htmlspecialchars($product['Nama_Produk']); ?></h3>
                <p>Warna: <?php echo htmlspecialchars($product['Warna_Produk']); ?></p>
                <p>Material: <?php echo htmlspecialchars($product['Material_Produk']); ?></p>
                <p>Harga: Rp<?php echo number_format($product['Harga_Produk'], 0, ',', '.'); ?></p>
                <p>Stok: <?php echo htmlspecialchars($product['Stok_Produk']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<div class="modal" id="addProductModal">
    <div class="modal-content">
        <span class="close-btn" onclick="toggleModal()">×</span>
        <h2>Add New Product</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="product_name">Product Name:</label>
                <input type="text" id="product_name" name="product_name" required>
            </div>
            <div class="form-group">
                <label for="product_color">Color:</label>
                <input type="text" id="product_color" name="product_color" required>
            </div>
            <div class="form-group">
                <label for="product_price">Price:</label>
                <input type="number" id="product_price" name="product_price" required>
            </div>
            <div class="form-group">
                <label for="product_material">Material:</label>
                <input type="text" id="product_material" name="product_material" required>
            </div>
            <div class="form-group">
                <label for="product_stock">Stock:</label>
                <input type="number" id="product_stock" name="product_stock" required>
            </div>
            <button type="submit" name="add_product" class="btn">Add Product</button>
        </form>
    </div>
</div>

</body>
</html>
