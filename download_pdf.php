<?php
include 'config.php';
checkLogin();

// Include FPDF library
require_once __DIR__ . '/fpdf/fpdf.php';

$user_id = $_SESSION['user_id'];
$ticket_id = (int)$_GET['id'];

$query = "SELECT * FROM tickets WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $ticket_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Ticket not found');
}

$ticket = $result->fetch_assoc();
$stmt->close();

// ===================================================================
// Generate Real PDF using FPDF
// ===================================================================

class TicketPDF extends FPDF {
    
    private $primaryR = 99;
    private $primaryG = 102;
    private $primaryB = 241;
    
    private $accentR = 168;
    private $accentG = 85;
    private $accentB = 247;
    
    function Header() {
        // No default header
    }
    
    function Footer() {
        $this->SetY(-20);
        $this->SetFont('Helvetica', '', 8);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 5, 'Santhosh Air Travels - Professional Flight Ticketing System', 0, 1, 'C');
        $this->Cell(0, 5, 'Generated on ' . date('d M Y H:i:s') . '  |  Thank you for booking with us!', 0, 0, 'C');
    }
    
    function DrawGradientHeader($title, $subtitle) {
        // Header background - gradient approximation using overlapping rects
        $this->SetFillColor($this->primaryR, $this->primaryG, $this->primaryB);
        $this->Rect(0, 0, 210, 42, 'F');
        
        // Add purple accent strip
        $this->SetFillColor($this->accentR, $this->accentG, $this->accentB);
        $this->Rect(140, 0, 70, 42, 'F');
        
        // Blend overlay
        $this->SetFillColor($this->primaryR, $this->primaryG, $this->primaryB);
        $this->SetAlpha(0.5);
        $this->Rect(130, 0, 80, 42, 'F');
        $this->SetAlpha(1);
        
        // Title
        $this->SetY(10);
        $this->SetFont('Helvetica', 'B', 22);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(0, 10, $this->safeUtf8($title), 0, 1, 'C');
        
        // Subtitle
        $this->SetFont('Helvetica', '', 11);
        $this->SetTextColor(230, 230, 255);
        $this->Cell(0, 7, $subtitle, 0, 1, 'C');
        
        $this->SetY(48);
    }
    
    function SetAlpha($alpha) {
        // FPDF basic doesn't support alpha, just ignore
    }
    
    function DrawSectionTitle($icon, $title) {
        $this->SetFont('Helvetica', 'B', 11);
        $this->SetFillColor(238, 242, 255);
        $this->SetTextColor($this->primaryR, $this->primaryG, $this->primaryB);
        $this->SetDrawColor($this->primaryR, $this->primaryG, $this->primaryB);
        
        // Left accent bar
        $y = $this->GetY();
        $this->SetFillColor($this->primaryR, $this->primaryG, $this->primaryB);
        $this->Rect(15, $y, 3, 8, 'F');
        
        // Background
        $this->SetFillColor(238, 242, 255);
        $this->Rect(18, $y, 177, 8, 'F');
        
        $this->SetX(22);
        $this->Cell(170, 8, $icon . '  ' . $title, 0, 1, 'L');
        $this->Ln(3);
    }
    
    function DrawFieldRow($label, $value) {
        $this->SetFont('Helvetica', '', 10);
        $this->SetTextColor(100, 100, 100);
        $this->SetX(20);
        $this->Cell(65, 7, $label, 0, 0, 'L');
        
        $this->SetFont('Helvetica', 'B', 10);
        $this->SetTextColor(30, 30, 30);
        $this->Cell(110, 7, $this->safeUtf8($value), 0, 1, 'R');
        
        // Separator line
        $this->SetDrawColor(230, 230, 230);
        $this->Line(20, $this->GetY(), 195, $this->GetY());
        $this->Ln(1);
    }
    
    function DrawBookingRef($ref) {
        $y = $this->GetY();
        
        // Background box
        $this->SetFillColor(238, 242, 255);
        $this->Rect(15, $y, 180, 18, 'F');
        
        // Left accent
        $this->SetFillColor($this->primaryR, $this->primaryG, $this->primaryB);
        $this->Rect(15, $y, 3, 18, 'F');
        
        // Label
        $this->SetXY(22, $y + 2);
        $this->SetFont('Helvetica', '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(50, 5, 'BOOKING REFERENCE', 0, 1, 'L');
        
        // Value
        $this->SetX(22);
        $this->SetFont('Helvetica', 'B', 16);
        $this->SetTextColor($this->primaryR, $this->primaryG, $this->primaryB);
        $this->Cell(50, 8, $ref, 0, 1, 'L');
        
        $this->SetY($y + 22);
    }
    
    function DrawStatus($status) {
        $y = $this->GetY();
        
        // Status colors
        switch ($status) {
            case 'confirmed':
                $this->SetFillColor(209, 250, 229);
                $this->SetTextColor(6, 95, 70);
                break;
            case 'pending':
                $this->SetFillColor(254, 249, 195);
                $this->SetTextColor(146, 64, 14);
                break;
            case 'cancelled':
                $this->SetFillColor(254, 226, 226);
                $this->SetTextColor(153, 27, 27);
                break;
            default:
                $this->SetFillColor(230, 230, 230);
                $this->SetTextColor(80, 80, 80);
        }
        
        // Centered status box
        $statusText = 'Status: ' . strtoupper($status);
        $w = $this->GetStringWidth($statusText) + 30;
        $x = (210 - $w) / 2;
        
        $this->SetFont('Helvetica', 'B', 11);
        $this->RoundedRect($x, $y, $w, 12, 3, 'F');
        $this->SetXY($x, $y + 2);
        $this->Cell($w, 8, $statusText, 0, 1, 'C');
        
        $this->Ln(5);
    }
    
    function RoundedRect($x, $y, $w, $h, $r, $style = '') {
        // Approximate rounded rect with regular rect for FPDF basic
        $this->Rect($x, $y, $w, $h, $style);
    }
    
    function DrawRoute($depCity, $arrCity, $depDate, $depTime, $arrTime) {
        $y = $this->GetY();
        
        // Background
        $this->SetFillColor(249, 250, 252);
        $this->Rect(15, $y, 180, 35, 'F');
        
        // Departure
        $this->SetXY(25, $y + 4);
        $this->SetFont('Helvetica', 'B', 18);
        $this->SetTextColor(30, 30, 30);
        $this->Cell(60, 8, $this->safeUtf8($depCity), 0, 1, 'C');
        
        $this->SetX(25);
        $this->SetFont('Helvetica', '', 9);
        $this->SetTextColor($this->primaryR, $this->primaryG, $this->primaryB);
        $this->Cell(60, 5, $depDate, 0, 1, 'C');
        
        $this->SetX(25);
        $this->SetFont('Helvetica', 'B', 10);
        $this->Cell(60, 5, $depTime, 0, 1, 'C');
        
        // Arrow in center
        $this->SetXY(90, $y + 8);
        $this->SetFont('Helvetica', 'B', 20);
        $this->SetTextColor($this->primaryR, $this->primaryG, $this->primaryB);
        $this->Cell(30, 10, '>>>', 0, 0, 'C');
        
        // Dotted line
        $this->SetDrawColor($this->primaryR, $this->primaryG, $this->primaryB);
        $lineY = $y + 17;
        $this->Line(93, $lineY, 117, $lineY);
        
        // Arrival
        $this->SetXY(125, $y + 4);
        $this->SetFont('Helvetica', 'B', 18);
        $this->SetTextColor(30, 30, 30);
        $this->Cell(60, 8, $this->safeUtf8($arrCity), 0, 1, 'C');
        
        $this->SetX(125);
        $this->SetFont('Helvetica', '', 9);
        $this->SetTextColor($this->primaryR, $this->primaryG, $this->primaryB);
        $this->Cell(60, 5, $depDate, 0, 1, 'C');
        
        $this->SetX(125);
        $this->SetFont('Helvetica', 'B', 10);
        $this->Cell(60, 5, $arrTime, 0, 1, 'C');
        
        $this->SetY($y + 40);
    }
    
    function DrawPricing($passengerCount, $pricePerPassenger, $totalPrice) {
        $y = $this->GetY();
        
        // Price rows background
        $this->SetFillColor(238, 242, 255);
        $this->Rect(15, $y, 180, 30, 'F');
        
        // Passenger count
        $this->SetXY(20, $y + 3);
        $this->SetFont('Helvetica', '', 10);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(80, 6, 'Number of Passengers:', 0, 0, 'L');
        $this->SetFont('Helvetica', 'B', 10);
        $this->SetTextColor(30, 30, 30);
        $this->Cell(85, 6, (string)$passengerCount, 0, 1, 'R');
        
        // Price per passenger (use Rs. instead of ₹ for FPDF compatibility)
        $this->SetX(20);
        $this->SetFont('Helvetica', '', 10);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(80, 6, 'Price per Passenger:', 0, 0, 'L');
        $this->SetFont('Helvetica', 'B', 10);
        $this->SetTextColor(30, 30, 30);
        $this->Cell(85, 6, 'Rs. ' . number_format($pricePerPassenger, 2), 0, 1, 'R');
        
        $this->Ln(2);
        
        // Total price box
        $totalY = $this->GetY();
        $this->SetFillColor($this->primaryR, $this->primaryG, $this->primaryB);
        $this->Rect(15, $totalY, 180, 14, 'F');
        
        $this->SetXY(20, $totalY + 3);
        $this->SetFont('Helvetica', 'B', 13);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(80, 8, 'TOTAL AMOUNT:', 0, 0, 'L');
        $this->Cell(85, 8, 'Rs. ' . number_format($totalPrice, 2), 0, 1, 'R');
        
        $this->SetY($totalY + 18);
    }
    
    function safeUtf8($text) {
        // Convert UTF-8 to ISO-8859-1 for FPDF compatibility
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $text);
    }
}

// Create PDF
$pdf = new TicketPDF();
$pdf->SetAutoPageBreak(true, 25);
$pdf->AddPage();

// Header
$pdf->DrawGradientHeader('Santhosh Air Travels', 'Professional Flight Ticket');

// Booking Reference
$pdf->DrawBookingRef($ticket['booking_reference']);

// Status
$pdf->DrawStatus($ticket['status']);

// Passenger Information
$pdf->DrawSectionTitle('', 'PASSENGER INFORMATION');
$pdf->DrawFieldRow('Passenger Name', $ticket['client_name']);
$pdf->DrawFieldRow('Email Address', $ticket['client_email'] ?: 'N/A');
$pdf->DrawFieldRow('Contact Number', $ticket['client_phone'] ?: 'N/A');

$pdf->Ln(3);

// Flight Information
$pdf->DrawSectionTitle('', 'FLIGHT INFORMATION');
$pdf->DrawFieldRow('Airline', $ticket['airline_name']);
$pdf->DrawFieldRow('Flight Number', $ticket['flight_number']);

$pdf->Ln(3);

// Journey Details
$pdf->DrawSectionTitle('', 'JOURNEY DETAILS');
$pdf->DrawRoute(
    $ticket['departure_city'],
    $ticket['arrival_city'],
    date('d M Y', strtotime($ticket['departure_date'])),
    date('h:i A', strtotime($ticket['departure_time'])),
    date('h:i A', strtotime($ticket['arrival_time']))
);

// Pricing Details
$pdf->DrawSectionTitle('', 'PRICING DETAILS');
$pdf->DrawPricing(
    $ticket['passenger_count'],
    $ticket['ticket_price'],
    $ticket['total_price']
);

// Notes
if (!empty($ticket['notes'])) {
    $pdf->Ln(3);
    $pdf->DrawSectionTitle('', 'ADDITIONAL NOTES');
    $y = $pdf->GetY();
    $pdf->SetFillColor(255, 250, 205);
    $pdf->Rect(15, $y, 180, 15, 'F');
    $pdf->SetFillColor(245, 158, 11);
    $pdf->Rect(15, $y, 3, 15, 'F');
    $pdf->SetXY(22, $y + 3);
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->MultiCell(165, 5, $pdf->safeUtf8($ticket['notes']), 0, 'L');
}

// Create PDF folder if not exists
$pdf_dir = __DIR__ . '/pdfs';
if (!is_dir($pdf_dir)) {
    mkdir($pdf_dir, 0755, true);
}

// Save and output
$pdf_filename = 'Ticket_' . $ticket['booking_reference'] . '.pdf';
$pdf_path = $pdf_dir . '/' . $pdf_filename;

// Save to file
$pdf->Output('F', $pdf_path);

// Update PDF path in database if column exists
$check_column_query = "SHOW COLUMNS FROM tickets LIKE 'pdf_path'";
$column_result = $conn->query($check_column_query);
if ($column_result && $column_result->num_rows > 0) {
    $update_query = "UPDATE tickets SET pdf_path = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    if ($update_stmt) {
        $pdf_relative_path = 'pdfs/' . $pdf_filename;
        $update_stmt->bind_param("si", $pdf_relative_path, $ticket_id);
        $update_stmt->execute();
        $update_stmt->close();
    }
}

// Send for download
$pdf->Output('D', $pdf_filename);
exit;
?>
