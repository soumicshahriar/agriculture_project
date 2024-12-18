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
// Handle search (if any)
$search_result = [];
if (isset($_GET['search'])) {
    $search_name = $_GET['search'];
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
        WHERE p.product_name LIKE ?";  // Changed to search by name

    $stmt = $conn->prepare($sql_search);
    $search_name = "%" . $search_name . "%";  // Allow partial matches
    $stmt->bind_param("s", $search_name);  // Bind search term
    $stmt->execute();
    $search_result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Delete the record from product_info table
    $sql = "DELETE FROM product_info WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Data deleted successfully!');
        setTimeout(function() {
    window.location.href = '/admin_db2/index.php'; // Redirect to another page
}, 0);
        
        </script>
        ";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
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
    <link rel="stylesheet" href="/admin_db2/product_table/product_info_style.css">

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
            margin-bottom: 5px;
            /* margin-top: 50px; */
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


    <!-- Search Bar -->
    <form method="get" action="">
        <input type="hidden" name="page" value="product">
        <label for="search">Search by Product Name: </label>
        <input type="text" id="search" name="search" placeholder="Enter Product Name" required>
        <button type="submit">Search</button>
    </form>
    <a href="/admin_db2/product_db/product.php"><button style="margin-right:auto">Add Product</button></a>
    <!-- Overlay Background for the Card -->
    <div class="overlay <?php echo (count($search_result) > 0) ? 'show' : ''; ?>"></div>

    <!-- Display multiple product cards based on search result -->


    <!-- Container for All Products Table -->
    <!-- Container for Search Results or All Products Table -->
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
            <th>Update</th>
            <th>Delete</th>
        </tr>

        <?php
        if (isset($_GET['search']) && !empty($search_result)) {
            // If a search has been made, display only the search results
            foreach ($search_result as $product_info) {
                echo "<tr>
                    <td>" . htmlspecialchars($product_info['product_id']) . "</td>
                    <td>" . htmlspecialchars($product_info['product_name']) . "</td>
                    <td>" . htmlspecialchars($product_info['category']) . "</td>
                    <td>" . htmlspecialchars($product_info['seasonality']) . "</td>
                    <td>" . htmlspecialchars($product_info['quantity']) . "</td>
                    <td>" . (is_null($product_info['new_price']) ? "NULL" : htmlspecialchars($product_info['new_price'])) . "</td>
                    <td>" . (is_null($product_info['old_price']) ? "NULL" : htmlspecialchars($product_info['old_price'])) . "</td>
                    <td>" . htmlspecialchars($product_info['production_date']) . "</td>
                    <td>" . htmlspecialchars($product_info['expiration_date']) . "</td>
                    <td>
                        <a href='/admin_db2/product_db/product.php?update=" . $product_info['id'] . "'>Edit</a> 
                    </td>
                    <td>
                        <a href='/admin_db2/product_table/product_info.php?delete=" . $product_info['id'] . "' onclick=\"return confirm('Are you sure?');\">Delete</a>
                    </td>
                </tr>";
            }
        } else {
            // Display all products if no search is made
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
                        <td>
                            <a href='/admin_db2/product_db/product.php?update=" . $row['id'] . "'>Edit</a> 
                        </td>
                        <td>
                            <a href='/admin_db2/product_table/product_info.php?delete=" . $row['id'] . "' onclick=\"return confirm('Are you sure?');\">Delete</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='9'>No products found.</td></tr>";
            }
        }
        ?>
    </table>

</body>

</html>

<?php
// Close connection
$conn->close();
?>