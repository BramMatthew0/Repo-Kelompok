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
$query_user = "SELECT Nama FROM pelanggan WHERE Username = ?";
$stmt = $conn->prepare($query_user);
$stmt->bind_param("s", $username);
$stmt->execute();
$result_user = $stmt->get_result();

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
    $query_check_cart = "SELECT * FROM keranjang WHERE Nama_Pelanggan_Keranjang = ? AND Id_Produk_Keranjang = ?";
    $stmt_check_cart = $conn->prepare($query_check_cart);
    $stmt_check_cart->bind_param("si", $customer_name, $product_id);
    $stmt_check_cart->execute();
    $result_check_cart = $stmt_check_cart->get_result();

    if ($result_check_cart->num_rows > 0) {
        // Update quantity if the product already exists in the cart
        $query_update_cart = "UPDATE keranjang 
                              SET Jumlah_Produk_Keranjang = Jumlah_Produk_Keranjang + 1 
                              WHERE Nama_Pelanggan_Keranjang = ? AND Id_Produk_Keranjang = ?";
        $stmt_update_cart = $conn->prepare($query_update_cart);
        $stmt_update_cart->bind_param("si", $customer_name, $product_id);
        $stmt_update_cart->execute();
    } else {
        // Insert a new row into the cart
        $query_insert_cart = "INSERT INTO keranjang 
                              (Nama_Pelanggan_Keranjang, Id_Produk_Keranjang, Nama_Produk_Keranjang, Material_Produk_Keranjang, Warna_Produk_Keranjang, Harga_Produk_Keranjang, Jumlah_Produk_Keranjang) 
                              VALUES (?, ?, ?, ?, ?, ?, 1)";
        $stmt_insert_cart = $conn->prepare($query_insert_cart);
        $stmt_insert_cart->bind_param("sisssd", $customer_name, $product_id, $product_name, $product_material, $product_color, $product_price);
        $stmt_insert_cart->execute();
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

// Function to categorize products based on the first word of the product name
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
    <title>Product List</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Styles */
        html {
            overflow-y: scroll;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            overflow-y: auto;
        }
        nav {
            background-color: #333;
            padding: 15px 0;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 1000;
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
            margin: 60px auto;
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
    </style>
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
            <div class="product-item" data-category="<?php echo htmlspecialchars(getCategory($product['Nama_Produk'])); ?>">
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
            const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
            if (scrollbarWidth > 0) {
                document.body.style.paddingRight = `${scrollbarWidth}px`;
            }
        });
</script>
</body>
</html>
