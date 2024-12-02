<?php
// Include the database connection file
include('../config/connect.php');

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch data from PRODUCT and product_info tables
$sql = "SELECT 
            pi.id,
            p.product_id, 
            p.product_name, 
            p.category, 
            pi.quantity, 
            pi.new_price, 
            pi.old_price, 
            pi.production_date, 
            pi.expiration_date 
        FROM 
            PRODUCT p
        INNER JOIN 
            product_info pi ON p.product_id = pi.product_id
        ORDER BY p.product_id";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Consumer Dashboard</title>
  <link rel="stylesheet" href="../product_db/product_info_style.css">
  <script>
    let totalCartPrice = 0;
    let cartItems = [];

    // Add item to the cart
    function addToCart(product_id, product_name, category, price) {
      const quantityInput = document.getElementById('quantity_' + product_id);
      const quantity = parseInt(quantityInput.value);
      const availableQuantity = parseInt(quantityInput.max);

      // Validation checks
      if (quantity <= 0) {
        alert("Error: Please enter a quantity greater than 0.");
        return;
      }

      if (availableQuantity <= 0) {
        alert("Error: The product is out of stock. You cannot add it to the cart.");
        return;
      }

      if (quantity > availableQuantity) {
        alert("Error: Please enter a quantity less than or equal to the available quantity (" + availableQuantity + ").");
        return;
      }

      // Check if the item is already in the cart
      const existingItem = cartItems.find(item => item.product_id === product_id);

      if (existingItem) {
        // Update the existing item's quantity and total price
        const oldTotal = existingItem.total_price;
        existingItem.quantity += quantity;
        existingItem.total_price = existingItem.quantity * price;
        updateTotalPrice(-oldTotal + existingItem.total_price);
        updateCartTableRow(existingItem);
      } else {
        // Add a new item to the cart
        const total_price = price * quantity;
        const purchaseDate = new Date().toLocaleDateString();

        cartItems.push({
          product_id: product_id,
          product_name: product_name,
          category: category,
          quantity: quantity,
          price: price,
          total_price: total_price,
          purchase_date: purchaseDate
        });

        addCartTableRow({
          product_id: product_id,
          product_name: product_name,
          category: category,
          quantity: quantity,
          price: price,
          total_price: total_price,
          purchase_date: purchaseDate
        });

        updateTotalPrice(total_price);
      }

      // Update the product quantity in the database via AJAX
      // updateProductQuantityInDatabase(product_id, quantity);

      // Update the available quantity on the front-end
      updateAvailableQuantity(product_id, quantity);
    }

    // Update the product quantity in the database when an item is added or removed
    function updateProductQuantityInDatabase(productId, quantityChanged) {
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "update_product_quantity.php", true);
      xhr.setRequestHeader("Content-Type", "application/json");

      xhr.onload = function() {
        if (xhr.status === 200) {
          const response = JSON.parse(xhr.responseText);
          if (response.status === "success") {
            console.log("Product quantity updated successfully.");
          } else {
            alert("Error: " + response.message);
          }
        } else {
          alert("Error: Unable to update product quantity.");
        }
      };

      // Send the product_id and quantity changed (negative for removal)
      xhr.send(JSON.stringify({
        product_id: productId,
        quantity_changed: quantityChanged
      }));
    }

    // Function to update the available quantity on the front-end
    function updateAvailableQuantity(product_id, quantityChanged) {
      const productRow = document.getElementById('product_row_' + product_id);
      const quantityCell = productRow.querySelector('.product-quantity');
      let availableQuantity = parseInt(quantityCell.innerText);

      // Subtract the added quantity from the available quantity
      availableQuantity -= quantityChanged;

      // Ensure that the available quantity never goes below zero
      if (availableQuantity < 0) {
        availableQuantity = 0;
      }

      // Update the quantity in the table cell
      quantityCell.innerText = availableQuantity;

      // Update the max value of the quantity input to reflect the new available stock
      const quantityInput = document.getElementById('quantity_' + product_id);
      quantityInput.max = availableQuantity;

      // Disable the "Add to Cart" button if stock reaches zero
      if (availableQuantity === 0) {
        const addButton = productRow.querySelector('.add-to-cart-btn');
        addButton.disabled = true;
      }
    }

    // Remove item from the cart and update the product quantity in the database
    function removeFromCart(button, itemPrice) {
      const row = button.parentNode.parentNode;
      const productName = row.cells[0].innerHTML;
      const productId = row.getAttribute('data-product-id'); // Get the product_id from the row
      const quantity = parseInt(row.cells[2].innerHTML); // Get the quantity of the product

      // Remove item from cartItems array
      cartItems = cartItems.filter(item => item.product_name !== productName);

      // Remove row from table
      row.parentNode.removeChild(row);

      // Update the total price
      updateTotalPrice(-itemPrice);

      // Update the product quantity in the database
      // updateProductQuantityInDatabase(productId, -quantity);
    }

    // Update the cart table row when quantity or price changes
    function updateCartTableRow(item) {
      const cartTable = document.getElementById("cartTable").getElementsByTagName('tbody')[0];
      const rows = cartTable.rows;

      for (let row of rows) {
        if (row.getAttribute('data-product-id') == item.product_id) {
          row.cells[2].innerHTML = item.quantity; // Update quantity
          row.cells[4].innerHTML = item.total_price; // Update total price
          break;
        }
      }
    }

    // Add a new row to the cart table
    function addCartTableRow(item) {
      const cartTable = document.getElementById("cartTable").getElementsByTagName('tbody')[0];

      const newRow = cartTable.insertRow();
      newRow.setAttribute('data-product-id', item.product_id); // Add a custom attribute to identify the product

      newRow.insertCell(0).innerHTML = item.product_name;
      newRow.insertCell(1).innerHTML = item.category;
      newRow.insertCell(2).innerHTML = item.quantity;
      newRow.insertCell(3).innerHTML = item.price;
      newRow.insertCell(4).innerHTML = item.total_price;
      newRow.insertCell(5).innerHTML = item.purchase_date;
      newRow.insertCell(6).innerHTML = `<button onclick="removeFromCart(this, ${item.total_price})">Remove</button>`;
    }

    // Update the total price
    function updateTotalPrice(amount) {
      totalCartPrice += amount;
      document.getElementById("totalPrice").innerHTML = "Total Price: $" + totalCartPrice.toFixed(2);
    }


    // Update the cart table row when quantity or price changes
    function updateCartTableRow(item) {
      const cartTable = document.getElementById("cartTable").getElementsByTagName('tbody')[0];
      const rows = cartTable.rows;

      for (let row of rows) {
        if (row.getAttribute('data-product-id') == item.product_id) {
          row.cells[2].innerHTML = item.quantity; // Update quantity
          row.cells[4].innerHTML = item.total_price; // Update total price
          break;
        }
      }
    }


    // Handle demand button click
    // Handle demand button click
    function demandProduct(product_id) {
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "update_demand_count.php", true);
      xhr.setRequestHeader("Content-Type", "application/json");

      xhr.onload = function() {
        if (xhr.status === 200) {
          const response = JSON.parse(xhr.responseText);
          if (response.status === "success") {
            alert("Thank you for your demand! This product has been added to the demand list.");
          } else {
            alert("Error: " + response.message);
          }
        } else {
          alert("Error: Unable to update product demand.");
        }
      };

      // Send the product_id as JSON
      xhr.send(JSON.stringify({
        product_id: product_id
      }));
    }


    // purchase item
    function purchaseItems() {
      if (cartItems.length > 0) {
        const consumer_id = 1; // Set the fixed consumer_id here
        const currentDate = new Date().toISOString().split('T')[0]; // Format as YYYY-MM-DD
        // Get the current date

        console.log('Cart Items:', cartItems); // Log cartItems before sending to the server

        // Send cart data to the server to store the purchase details
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "store_purchase.php", true);
        xhr.setRequestHeader("Content-Type", "application/json");

        xhr.onload = function() {
          if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === "success") {
              alert("Purchase successful!");
              console.log("Server response:", response);
              // updateProductQuantityInDatabase(product_id, quantity);

              // Clear the cart and total price
              cartItems = [];
              totalCartPrice = 0;
              document.getElementById("totalPrice").innerHTML = "Total Price: $0.00";
              // renderCartTable();
              location.reload();
            } else {
              alert("Failed to store purchase data: " + response.message);
              console.error("Error details:", response.message);
            }
          } else {
            alert("Purchase failed. Please try again.");
          }
        };

        // Send cartItems, consumer_id, and current date as JSON
        xhr.send(JSON.stringify({
          cartItems: cartItems,
          consumer_id: consumer_id,
          purchase_date: currentDate
        }));
      } else {
        alert("Your cart is empty.");
      }
    }
  </script>
</head>

<body>

  <h3>Products</h3>

  <!-- Display Data from PRODUCT and product_info Tables -->
  <table class="product-table">
    <thead>
      <tr>
        <th>Product Name</th>
        <th>Category</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Quantity to Purchase</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr id="product_row_<?= $row['product_id'] ?>">
          <td><?= htmlspecialchars($row['product_name']) ?></td>
          <td><?= htmlspecialchars($row['category']) ?></td>
          <td class="product-quantity"><?= htmlspecialchars($row['quantity']) ?></td>
          <td><?= (is_null($row['new_price']) ? "NULL" : htmlspecialchars($row['new_price'])) ?></td>
          <td>
            <input type="number" id="quantity_<?= $row['product_id'] ?>" min="1" max="<?= $row['quantity'] ?>" value="1" />
          </td>
          <td>
            <?php if ($row['quantity'] > 0): ?>
              <button class="add-to-cart-btn" onclick="addToCart(<?= $row['product_id'] ?>, '<?= addslashes($row['product_name']) ?>', '<?= addslashes($row['category']) ?>', <?= $row['new_price'] ?>)">Add to Cart</button>
            <?php else: ?>
              <button class="demand-btn" onclick="demandProduct(<?= $row['product_id'] ?>)">Demand</button>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <h3>Your Cart</h3>
  <!-- Cart Summary Table -->
  <table border="1" id="cartTable">
    <thead>
      <tr>
        <th>Product Name</th>
        <th>Category</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Total Price</th>
        <th>Purchase Date</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <!-- Cart items will be added here dynamically -->
    </tbody>
  </table>

  <!-- Total Price of Cart -->
  <h4 id="totalPrice">Total Price: $0.00</h4>

  <!-- Purchase Button -->
  <button onclick="purchaseItems()">Purchase</button>

</body>

</html>

<?php
// Close connection
$conn->close();
?>