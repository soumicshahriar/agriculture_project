<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market Price Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Market Price Data</h1>
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody id="price-data"></tbody>
        </table>
    </div>

    <!-- Include jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            // Fetch market data from the PHP backend
            fetch('fetch_market_data.php')
                .then(response => response.json())
                .then(data => {
                    let rows = '';
                    data.forEach(row => {
                        rows += `<tr>
                                    <td>${row.product_name}</td>
                                    <td>${row.price}</td>
                                    <td>${row.date}</td>
                                  </tr>`;
                    });
                    $('#price-data').html(rows); // Insert data into the table body
                });
        });
    </script>
</body>
</html>
