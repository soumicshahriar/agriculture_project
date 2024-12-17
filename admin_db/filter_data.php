<?php
// Assuming the same database connection is included
include('../config/connect.php');

$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';

// Build the query with the date range filter
$query = "
    SELECT p.product_name, cph.price, SUM(cph.quantity) AS total_quantity, cph.purchase_date
    FROM customer_purchase_history cph
    JOIN product p ON cph.product_id = p.product_id
";

if ($startDate && $endDate) {
 $query .= " WHERE cph.purchase_date BETWEEN '$startDate' AND '$endDate'";
}

$query .= " GROUP BY p.product_name, cph.price, cph.purchase_date ORDER BY p.product_name, cph.price, cph.purchase_date";

$result = $conn->query($query);

$productNames = [];
$quantities = [];
$prices = [];
$priceElasticities = [];
$purchaseDates = [];

if ($result->num_rows > 0) {
 $previousData = [];
 while ($row = $result->fetch_assoc()) {
  $productName = $row['product_name'];
  $price = $row['price'];
  $quantity = $row['total_quantity'];
  $purchaseDate = $row['purchase_date'];

  if (isset($previousData[$productName])) {
   $previousPrice = $previousData[$productName]['price'];
   $previousQuantity = $previousData[$productName]['quantity'];

   // Calculate price elasticity of demand (PED)
   $priceChange = (($price - $previousPrice) / $previousPrice) * 100;
   $quantityChange = (($quantity - $previousQuantity) / $previousQuantity) * 100;

   if ($priceChange != 0) {
    $ped = $quantityChange / $priceChange;
   } else {
    $ped = 0;
   }

   $productNames[] = $productName;
   $prices[] = $price;
   $quantities[] = $quantity;
   $purchaseDates[] = $purchaseDate;
   $priceElasticities[] = round($ped, 2);
  }

  $previousData[$productName] = ['price' => $price, 'quantity' => $quantity];
 }
}

echo json_encode([
 'productNames' => $productNames,
 'quantities' => $quantities,
 'prices' => $prices,
 'purchaseDates' => $purchaseDates,
 'priceElasticities' => $priceElasticities
]);
