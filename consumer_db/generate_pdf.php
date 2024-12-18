<?php
require('fpdf/fpdf.php');

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);
$cartItems = $data['cartItems'] ?? [];

// Create PDF instance
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Add title
$pdf->Cell(0, 10, 'Cart Summary', 0, 1, 'C');
$pdf->Ln(10);

// Add table headers
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(50, 10, 'Product Name', 1);
$pdf->Cell(50, 10, 'Category', 1);
$pdf->Cell(30, 10, 'Quantity', 1);
$pdf->Cell(30, 10, 'Price', 1);
$pdf->Cell(30, 10, 'Total', 1);
$pdf->Ln();

// Add table data
$pdf->SetFont('Arial', '', 12);
foreach ($cartItems as $item) {
    $pdf->Cell(50, 10, $item['product_name'], 1);
    $pdf->Cell(50, 10, $item['category'], 1);
    $pdf->Cell(30, 10, $item['quantity'], 1);
    $pdf->Cell(30, 10, '$' . $item['price'], 1);
    $pdf->Cell(30, 10, '$' . $item['total_price'], 1);
    $pdf->Ln();
}

// Output PDF to browser
$pdf->Output('D', 'cart_summary.pdf');
