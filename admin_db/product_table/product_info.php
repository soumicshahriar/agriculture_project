<?php
// Database credentials
$host = "localhost";
$username = "root";
$password = "";
$database = "agriculture_product_data";

// Connect to the database
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle search (if any)
$search_result = [];
if (isset($_GET['search'])) {
    $search_id = $_GET['search'];
    $sql_search = "SELECT 
            pi.id,
            p.product_id, 
            p.product_name, 
            p.category,
            p.seasonality, 
            pi.quantity, 
            pi.new_price, 
            pi.old_price, 
            pi.production_date, 
            pi.expiration_date 
        FROM 
            PRODUCT p
        LEFT JOIN 
            product_info pi ON p.product_id = pi.product_id
        WHERE p.product_id = ?";

    $stmt = $conn->prepare($sql_search);
    $stmt->bind_param("s", $search_id);
    $stmt->execute();
    $search_result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Fetch all products from PRODUCT and product_info tables
$sql_all_products = "SELECT 
                    pi.id,
                    p.product_id, 
                    p.product_name, 
                    p.category,
                    p.seasonality, 
                    pi.quantity, 
                    pi.new_price, 
                    pi.old_price, 
                    pi.production_date, 
                    pi.expiration_date 
                FROM 
                    PRODUCT p
                LEFT JOIN 
                    product_info pi ON p.product_id = pi.product_id
                ORDER BY p.product_id";
$all_products_result = $conn->query($sql_all_products);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Info Form</title>
    <link rel="stylesheet" href="/admin_db/product_table/product_info_style.css">

    <style>
        /* Container for all the cards */
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        /* Style for the individual cards */
        .card {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 30%;
            margin: 10px 0;
            box-sizing: border-box;
        }

        .card h2 {
            text-align: center;
        }

        .card p {
            margin: 10px 0;
        }

        .card p strong {
            color: #333;
        }

        /* Style for the search form */
        form {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Table Style */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <a href="/admin_db/product_db/product_info.php"><button>Add/Update Product Info</button></a>

    <h2>Search Product by Product ID</h2>

    <!-- Search Bar -->
    <form method="get" action="">
        <label for="search">Search by Product ID: </label>
        <input type="text" id="search" name="search" placeholder="Enter Product ID" required>
        <button type="submit">Search</button>
    </form>

    <!-- Overlay Background for the Card -->
    <div class="overlay <?php echo (count($search_result) > 0) ? 'show' : ''; ?>"></div>

    <!-- Display multiple product cards based on search result -->
    <?php if (!empty($search_result)): ?>
        <h2>Search Results</h2>
        <div class="card-container">
            <?php foreach ($search_result as $product_info): ?>
                <div class="card">
                    <h2>Product ID: <?php echo htmlspecialchars($product_info['product_id']); ?></h2>
                    <p><strong>Product Name:</strong> <?php echo htmlspecialchars($product_info['product_name']); ?></p>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($product_info['category']); ?></p>
                    <p><strong>Seasonality:</strong> <?php echo htmlspecialchars($product_info['seasonality']); ?></p>
                    <p><strong>Quantity:</strong> <?php echo htmlspecialchars($product_info['quantity']); ?></p>
                    <p><strong>New Price:</strong> <?php echo (is_null($product_info['new_price']) ? "NULL" : htmlspecialchars($product_info['new_price'])); ?></p>
                    <p><strong>Old Price:</strong> <?php echo (is_null($product_info['old_price']) ? "NULL" : htmlspecialchars($product_info['old_price'])); ?></p>
                    <p><strong>Production Date:</strong> <?php echo htmlspecialchars($product_info['production_date']); ?></p>
                    <p><strong>Expiration Date:</strong> <?php echo htmlspecialchars($product_info['expiration_date']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif (isset($_GET['search'])): ?>
        <p>No product found with this ID.</p>
    <?php endif; ?>

    <!-- Container for All Products Table -->
    <h2>All Products</h2>
    <table>
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Category</th>
            <th>Seasonality</th>
            <th>Quantity</th>
            <th>New Price</th>
            <th>Old Price</th>
            <th>Production Date</th>
            <th>Expiration Date</th>
        </tr>
        <?php
        if ($all_products_result->num_rows > 0) {
            while ($row = $all_products_result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['product_id']) . "</td>
                        <td>" . htmlspecialchars($row['product_name']) . "</td>
                        <td>" . htmlspecialchars($row['category']) . "</td>
                        <td>" . htmlspecialchars($row['seasonality']) . "</td>
                        <td>" . htmlspecialchars($row['quantity']) . "</td>
                        <td>" . (is_null($row['new_price']) ? "NULL" : htmlspecialchars($row['new_price'])) . "</td>
                        <td>" . (is_null($row['old_price']) ? "NULL" : htmlspecialchars($row['old_price'])) . "</td>
                        <td>" . htmlspecialchars($row['production_date']) . "</td>
                        <td>" . htmlspecialchars($row['expiration_date']) . "</td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No products found.</td></tr>";
        }
        ?>
    </table>

</body>

</html>

<?php
// Close connection
$conn->close();
?>