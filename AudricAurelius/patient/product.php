<?php 
// Start session
session_start();

// Include database connection
require_once('../config/db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Get the logged-in user's data
$username = $_SESSION['username'];
$query_user = "SELECT Nama FROM pelanggan WHERE Username = '$username'";
$result_user = $conn->query($query_user);

// Check for query error
if (!$result_user || $result_user->num_rows == 0) {
    die("User not found or query failed.");
}

// Fetch the user's name
$user_data = $result_user->fetch_assoc();
$customer_name = $user_data['Nama'];

// Handle Add to Cart functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_material = $_POST['product_material'];
    $product_color = $_POST['product_color'];

    // Check if the product is already in the cart for this customer
    $query_check_cart = "SELECT * FROM keranjang WHERE Nama_Pelanggan_Keranjang = '$customer_name' AND Id_Produk_Keranjang = '$product_id'";
    $result_check_cart = $conn->query($query_check_cart);

    if ($result_check_cart->num_rows > 0) {
        // Update quantity if the product already exists in the cart
        $query_update_cart = "UPDATE keranjang 
                              SET Jumlah_Produk_Keranjang = Jumlah_Produk_Keranjang + 1 
                              WHERE Nama_Pelanggan_Keranjang = '$customer_name' AND Id_Produk_Keranjang = '$product_id'";
        $conn->query($query_update_cart);
    } else {
        // Insert a new row into the cart
        $query_insert_cart = "INSERT INTO keranjang 
                              (Nama_Pelanggan_Keranjang, Id_Produk_Keranjang, Nama_Produk_Keranjang, Material_Produk_Keranjang, Warna_Produk_Keranjang, Harga_Produk_Keranjang, Jumlah_Produk_Keranjang) 
                              VALUES 
                              ('$customer_name', '$product_id', '$product_name', '$product_material', '$product_color', '$product_price', 1)";
        $conn->query($query_insert_cart);
    }

    // Feedback message
    $success_message = "Produk berhasil ditambahkan ke keranjang!";
}

// Query to fetch products from 'produk' table
$query_products = "SELECT * FROM produk";
$result = $conn->query($query_products);

// Check for query error
if (!$result) {
    die("Query gagal: " . $conn->error);
}

// Fetch products as an associative array
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Sama seperti style sebelumnya */
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

        .product-section {
            padding: 20px;
        }

        .product-section h2 {
            text-align: center;
            margin-bottom: 20px;
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
            padding: 20px;
        }

        .product-item {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .add-to-cart-btn {
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .add-to-cart-btn:hover {
            background-color: #0056b3;
        }

        .success-message {
            color: green;
            text-align: center;
            margin-bottom: 20px;
        }

        @media (max-width: 900px) {
            .product-list {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .product-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <script>
        // JavaScript for filtering products by category
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
        <li><a href="pelanggan_dashboard.php">Home</a></li>
        <li><a href="review.php">Review</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="product.php">Product</a></li>
        <li><a href="history.php">History</a></li>
        <li><a href="logout.php">Logout</a></li>
        <li><a href="cart.php"><i class="fas fa-shopping-cart cart-icon"></i></a></li>
    </ul>
</nav>

<section class="product-section">
    <h2>Product List</h2>
    <?php if (isset($success_message)): ?>
        <p class="success-message"><?php echo $success_message; ?></p>
    <?php endif; ?>
    <div class="tabs">
        <div class="tab active" id="tab-all" onclick="filterProducts('all')">All</div>
        <div class="tab" id="tab-komponen" onclick="filterProducts('komponen')">Komponen</div>
        <div class="tab" id="tab-meja" onclick="filterProducts('meja')">Meja</div>
        <div class="tab" id="tab-kursi" onclick="filterProducts('kursi')">Kursi</div>
        <div class="tab" id="tab-lemari" onclick="filterProducts('lemari')">Lemari</div>
        <div class="tab" id="tab-kasur" onclick="filterProducts('kasur')">Kasur</div>
    </div>
    <div class="product-list">
        <?php foreach ($products as $product): ?>
            <?php
                // Categorize products based on the product name
                $category = strtolower($product['Nama_Produk']);
            ?>
            <div class="product-item" data-category="<?php echo htmlspecialchars($category); ?>">
                <h3><?php echo htmlspecialchars($product['Nama_Produk']); ?></h3>
                <p>Warna: <?php echo htmlspecialchars($product['Warna_Produk']); ?></p>
                <p>Material: <?php echo htmlspecialchars($product['Material_Produk']); ?></p>
                <p>Harga: Rp<?php echo number_format($product['Harga_Produk'], 0, ',', '.'); ?></p>
                <p>Stok: <?php echo htmlspecialchars($product['Stok_Produk']); ?></p>
                <form method="POST" action="">
                    <input type="hidden" name="product_id" value="<?php echo $product['Id_Produk']; ?>">
                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['Nama_Produk']); ?>">
                    <input type="hidden" name="product_material" value="<?php echo htmlspecialchars($product['Material_Produk']); ?>">
                    <input type="hidden" name="product_color" value="<?php echo htmlspecialchars($product['Warna_Produk']); ?>">
                    <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($product['Harga_Produk']); ?>">
                    <button type="submit" name="add_to_cart" class="add-to-cart-btn">Add to Cart</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</section>

</body>
</html>
