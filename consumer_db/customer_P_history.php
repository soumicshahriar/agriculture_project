<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Purchase History</title>
    <link rel="stylesheet" href="customer_d.css">
</head>
<body>
    <header>
        <h1>Purchase History</h1>
    </header>
    <section id="purchase-history">
        <!-- Purchase history will be loaded here -->
    </section>
    <script>
        async function loadPurchaseHistory() {
            <?php
            session_start();
            if (!isset($_SESSION['userId'])) {
                $_SESSION['userId'] = 1; // Temporary fallback for testing
            }
            ?>
            const consumerId = <?php echo json_encode($_SESSION['userId']); ?>;

            if (!consumerId) {
                document.getElementById('purchase-history').innerHTML = `<p>Consumer ID not found.</p>`;
                return;
            }

            try {
                const response = await fetch(`customer_d.php?id=${consumerId}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();
                const historySection = document.getElementById('purchase-history');

                if (data.error) {
                    historySection.innerHTML = `<p>${data.error}</p>`;
                } else if (Array.isArray(data) && data.length > 0) {
                    const table = document.createElement('table');
                    table.innerHTML = `
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total Price</th>
                            <th>Purchase Date</th>
                        </tr>`;
                    data.forEach(item => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${item.product_name}</td>
                            <td>${item.quantity}</td>
                            <td>${parseFloat(item.price).toFixed(2)}</td>
                            <td>${parseFloat(item.total_price).toFixed(2)}</td>
                            <td>${item.purchase_date}</td>`;
                        table.appendChild(row);
                    });
                    historySection.innerHTML = ''; // Clear previous content
                    historySection.appendChild(table);
                } else {
                    historySection.innerHTML = `<p>No purchase history found.</p>`;
                }
            } catch (error) {
                console.error('Error fetching purchase history:', error);
                document.getElementById('purchase-history').innerHTML = `<p>Unable to load purchase history. Please check the console for more details.</p>`;
            }
        }

        loadPurchaseHistory();
    </script>
</body>
</html>
