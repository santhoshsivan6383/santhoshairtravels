<?php
/**
 * Ticket Controller
 * Handles all ticket CRUD operations and PDF generation
 * 
 * Routes:
 *   /ticket/dashboard     → List all tickets
 *   /ticket/add           → Create new ticket
 *   /ticket/edit/{id}     → Edit a ticket
 *   /ticket/view/{id}     → View ticket details
 *   /ticket/delete/{id}   → Delete a ticket
 *   /ticket/pdf/{id}      → Download ticket PDF
 */
class TicketController extends Controller {
    private $ticketModel;

    public function __construct() {
        parent::__construct();
        $this->requireLogin();
        $this->ticketModel = new TicketModel();
    }

    /**
     * Dashboard - List all tickets with stats
     * Route: /ticket/dashboard
     */
    public function dashboard() {
        $userId = $this->getUserId();
        $tickets = $this->ticketModel->getUserTickets($userId);
        $stats = $this->ticketModel->getStats($userId);

        $this->render('tickets/dashboard', [
            'tickets'        => $tickets,
            'total_tickets'  => $stats['total'],
            'confirmed_count'=> $stats['confirmed'],
            'pending_count'  => $stats['pending'],
            'total_revenue'  => $stats['revenue'],
            'username'       => $this->getUsername(),
            'success'        => $this->getFlash('success'),
            'error'          => $this->getFlash('error'),
            'page_title'     => 'Dashboard'
        ]);
    }

    /**
     * Add new ticket
     * Route: /ticket/add
     */
    public function add() {
        $error = '';
        $success = '';

        if ($this->isPost()) {
            $data = [
                'user_id'        => $this->getUserId(),
                'client_name'    => $this->sanitize($this->postData('client_name')),
                'client_email'   => $this->sanitize($this->postData('client_email')),
                'client_phone'   => $this->sanitize($this->postData('client_phone')),
                'departure_city' => $this->sanitize($this->postData('departure_city')),
                'arrival_city'   => $this->sanitize($this->postData('arrival_city')),
                'departure_date' => $this->sanitize($this->postData('departure_date')),
                'departure_time' => $this->sanitize($this->postData('departure_time')),
                'arrival_time'   => $this->sanitize($this->postData('arrival_time')),
                'airline_name'   => $this->sanitize($this->postData('airline_name')),
                'flight_number'  => $this->sanitize($this->postData('flight_number')),
                'passenger_count'=> (int)$this->postData('passenger_count', 1),
                'ticket_price'   => (float)$this->postData('ticket_price', 0),
                'status'         => $this->sanitize($this->postData('status', 'pending')),
                'notes'          => $this->sanitize($this->postData('notes')),
            ];

            // Validation
            if (empty($data['client_name']) || empty($data['departure_city']) || 
                empty($data['arrival_city']) || empty($data['departure_date'])) {
                $error = 'Please fill all required fields!';
            } else {
                $ticketId = $this->ticketModel->createTicket($data);
                if ($ticketId) {
                    $ticket = $this->ticketModel->find($ticketId);
                    $this->setFlash('success', 'Ticket created! Ref: ' . $ticket['booking_reference']);
                    $this->redirect('ticket/dashboard');
                } else {
                    $error = 'Error creating ticket. Please try again.';
                }
            }
        }

        $this->render('tickets/add', [
            'error'      => $error,
            'success'    => $success,
            'page_title' => 'Add Ticket'
        ]);
    }

    /**
     * Edit a ticket
     * Route: /ticket/edit/{id}
     */
    public function edit($id = null) {
        $ticketId = (int)($id ?? $this->getData('id'));
        if (!$ticketId) {
            $this->redirect('ticket/dashboard');
        }

        $userId = $this->getUserId();
        $ticket = $this->ticketModel->getTicketForUser($ticketId, $userId);

        if (!$ticket) {
            $this->setFlash('error', 'Ticket not found');
            $this->redirect('ticket/dashboard');
        }

        $error = '';
        $success = '';

        if ($this->isPost()) {
            $data = [
                'client_name'    => $this->sanitize($this->postData('client_name')),
                'client_email'   => $this->sanitize($this->postData('client_email')),
                'client_phone'   => $this->sanitize($this->postData('client_phone')),
                'departure_city' => $this->sanitize($this->postData('departure_city')),
                'arrival_city'   => $this->sanitize($this->postData('arrival_city')),
                'departure_date' => $this->sanitize($this->postData('departure_date')),
                'departure_time' => $this->sanitize($this->postData('departure_time')),
                'arrival_time'   => $this->sanitize($this->postData('arrival_time')),
                'airline_name'   => $this->sanitize($this->postData('airline_name')),
                'flight_number'  => $this->sanitize($this->postData('flight_number')),
                'passenger_count'=> (int)$this->postData('passenger_count', 1),
                'ticket_price'   => (float)$this->postData('ticket_price', 0),
                'status'         => $this->sanitize($this->postData('status')),
                'notes'          => $this->sanitize($this->postData('notes')),
            ];

            // Calculate total price
            $data['total_price'] = $data['passenger_count'] * $data['ticket_price'];

            // Validation
            if (empty($data['client_name']) || empty($data['departure_city']) || 
                empty($data['arrival_city']) || empty($data['departure_date'])) {
                $error = 'Please fill all required fields!';
            } else {
                if ($this->ticketModel->updateTicket($ticketId, $userId, $data)) {
                    $success = 'Ticket updated successfully!';
                    // Refresh ticket data
                    $ticket = $this->ticketModel->getTicketForUser($ticketId, $userId);
                } else {
                    $error = 'Error updating ticket. Please try again.';
                }
            }
        }

        $this->render('tickets/edit', [
            'ticket'     => $ticket,
            'ticket_id'  => $ticketId,
            'error'      => $error,
            'success'    => $success,
            'page_title' => 'Edit Ticket'
        ]);
    }

    /**
     * View ticket details
     * Route: /ticket/view/{id}
     */
    public function view($id = null) {
        $ticketId = (int)($id ?? $this->getData('id'));
        if (!$ticketId) {
            $this->redirect('ticket/dashboard');
        }

        $userId = $this->getUserId();
        $ticket = $this->ticketModel->getTicketForUser($ticketId, $userId);

        if (!$ticket) {
            $this->setFlash('error', 'Ticket not found');
            $this->redirect('ticket/dashboard');
        }

        $this->render('tickets/view', [
            'ticket'     => $ticket,
            'page_title' => 'View Ticket'
        ]);
    }

    /**
     * Delete a ticket
     * Route: /ticket/delete/{id}
     */
    public function delete($id = null) {
        $ticketId = (int)($id ?? $this->getData('id'));
        if (!$ticketId) {
            $this->redirect('ticket/dashboard');
        }

        $userId = $this->getUserId();
        if ($this->ticketModel->deleteTicket($ticketId, $userId)) {
            $this->setFlash('success', 'Ticket deleted successfully!');
        } else {
            $this->setFlash('error', 'Error deleting ticket');
        }

        $this->redirect('ticket/dashboard');
    }

    /**
     * Download ticket as PDF (real FPDF)
     * Route: /ticket/pdf/{id}
     */
    public function pdf($id = null) {
        $ticketId = (int)($id ?? $this->getData('id'));
        if (!$ticketId) {
            $this->redirect('ticket/dashboard');
        }

        $userId = $this->getUserId();
        $ticket = $this->ticketModel->getTicketForUser($ticketId, $userId);

        if (!$ticket) {
            die('Ticket not found');
        }

        // Include FPDF
        require_once FPDF_PATH;

        // Generate PDF
        $pdf = $this->buildPdf($ticket);

        // Save to pdfs folder
        $pdfDir = dirname(FPDF_PATH) . '/../pdfs';
        if (!is_dir($pdfDir)) {
            mkdir($pdfDir, 0755, true);
        }
        $filename = 'Ticket_' . $ticket['booking_reference'] . '.pdf';
        $pdf->Output('F', $pdfDir . '/' . $filename);

        // Send to browser for download
        $pdf->Output('D', $filename);
        exit;
    }

    /**
     * Build the PDF document using FPDF
     */
    private function buildPdf($ticket) {
        $pdf = new FPDF();
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->AddPage();

        // Colors
        $pR = 99; $pG = 102; $pB = 241; // Primary indigo
        $aR = 168; $aG = 85; $aB = 247; // Accent purple

        // ── HEADER ──
        $pdf->SetFillColor($pR, $pG, $pB);
        $pdf->Rect(0, 0, 210, 42, 'F');
        $pdf->SetFillColor($aR, $aG, $aB);
        $pdf->Rect(140, 0, 70, 42, 'F');

        $pdf->SetY(10);
        $pdf->SetFont('Helvetica', 'B', 22);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 10, $this->pdfSafe('Santhosh Air Travels'), 0, 1, 'C');
        $pdf->SetFont('Helvetica', '', 11);
        $pdf->SetTextColor(230, 230, 255);
        $pdf->Cell(0, 7, 'Professional Flight Ticket', 0, 1, 'C');
        $pdf->SetY(48);

        // ── BOOKING REFERENCE ──
        $y = $pdf->GetY();
        $pdf->SetFillColor(238, 242, 255);
        $pdf->Rect(15, $y, 180, 18, 'F');
        $pdf->SetFillColor($pR, $pG, $pB);
        $pdf->Rect(15, $y, 3, 18, 'F');

        $pdf->SetXY(22, $y + 2);
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(50, 5, 'BOOKING REFERENCE', 0, 1, 'L');
        $pdf->SetX(22);
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->SetTextColor($pR, $pG, $pB);
        $pdf->Cell(50, 8, $ticket['booking_reference'], 0, 1, 'L');
        $pdf->SetY($y + 22);

        // ── STATUS ──
        $this->pdfStatus($pdf, $ticket['status']);

        // ── PASSENGER INFO ──
        $this->pdfSectionTitle($pdf, 'PASSENGER INFORMATION', $pR, $pG, $pB);
        $this->pdfField($pdf, 'Passenger Name', $ticket['client_name']);
        $this->pdfField($pdf, 'Email Address', $ticket['client_email'] ?: 'N/A');
        $this->pdfField($pdf, 'Contact Number', $ticket['client_phone'] ?: 'N/A');
        $pdf->Ln(3);

        // ── FLIGHT INFO ──
        $this->pdfSectionTitle($pdf, 'FLIGHT INFORMATION', $pR, $pG, $pB);
        $this->pdfField($pdf, 'Airline', $ticket['airline_name']);
        $this->pdfField($pdf, 'Flight Number', $ticket['flight_number']);
        $pdf->Ln(3);

        // ── ROUTE ──
        $this->pdfSectionTitle($pdf, 'JOURNEY DETAILS', $pR, $pG, $pB);
        $this->pdfRoute(
            $pdf, $ticket, $pR, $pG, $pB
        );

        // ── PRICING ──
        $this->pdfSectionTitle($pdf, 'PRICING DETAILS', $pR, $pG, $pB);
        $this->pdfPricing($pdf, $ticket, $pR, $pG, $pB);

        // ── NOTES ──
        if (!empty($ticket['notes'])) {
            $pdf->Ln(3);
            $this->pdfSectionTitle($pdf, 'ADDITIONAL NOTES', $pR, $pG, $pB);
            $y = $pdf->GetY();
            $pdf->SetFillColor(255, 250, 205);
            $pdf->Rect(15, $y, 180, 15, 'F');
            $pdf->SetFillColor(245, 158, 11);
            $pdf->Rect(15, $y, 3, 15, 'F');
            $pdf->SetXY(22, $y + 3);
            $pdf->SetFont('Helvetica', '', 10);
            $pdf->SetTextColor(80, 80, 80);
            $pdf->MultiCell(165, 5, $this->pdfSafe($ticket['notes']), 0, 'L');
        }

        // ── FOOTER ──
        $pdf->SetY(-20);
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->SetTextColor(150, 150, 150);
        $pdf->Cell(0, 5, 'Santhosh Air Travels - Professional Flight Ticketing System', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Generated on ' . date('d M Y H:i:s') . '  |  Thank you for booking with us!', 0, 0, 'C');

        return $pdf;
    }

    private function pdfSafe($text) {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $text);
    }

    private function pdfSectionTitle($pdf, $title, $r, $g, $b) {
        $y = $pdf->GetY();
        $pdf->SetFillColor($r, $g, $b);
        $pdf->Rect(15, $y, 3, 8, 'F');
        $pdf->SetFillColor(238, 242, 255);
        $pdf->Rect(18, $y, 177, 8, 'F');
        $pdf->SetFont('Helvetica', 'B', 11);
        $pdf->SetTextColor($r, $g, $b);
        $pdf->SetXY(22, $y);
        $pdf->Cell(170, 8, $title, 0, 1, 'L');
        $pdf->Ln(3);
    }

    private function pdfField($pdf, $label, $value) {
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->SetX(20);
        $pdf->Cell(65, 7, $label, 0, 0, 'L');
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->Cell(110, 7, $this->pdfSafe($value), 0, 1, 'R');
        $pdf->SetDrawColor(230, 230, 230);
        $pdf->Line(20, $pdf->GetY(), 195, $pdf->GetY());
        $pdf->Ln(1);
    }

    private function pdfStatus($pdf, $status) {
        $y = $pdf->GetY();
        switch ($status) {
            case 'confirmed': $pdf->SetFillColor(209, 250, 229); $pdf->SetTextColor(6, 95, 70); break;
            case 'pending':   $pdf->SetFillColor(254, 249, 195); $pdf->SetTextColor(146, 64, 14); break;
            case 'cancelled': $pdf->SetFillColor(254, 226, 226); $pdf->SetTextColor(153, 27, 27); break;
            default:          $pdf->SetFillColor(230, 230, 230); $pdf->SetTextColor(80, 80, 80);
        }
        $text = 'Status: ' . strtoupper($status);
        $w = $pdf->GetStringWidth($text) + 30;
        $x = (210 - $w) / 2;
        $pdf->SetFont('Helvetica', 'B', 11);
        $pdf->Rect($x, $y, $w, 12, 'F');
        $pdf->SetXY($x, $y + 2);
        $pdf->Cell($w, 8, $text, 0, 1, 'C');
        $pdf->Ln(5);
    }

    private function pdfRoute($pdf, $ticket, $r, $g, $b) {
        $y = $pdf->GetY();
        $pdf->SetFillColor(249, 250, 252);
        $pdf->Rect(15, $y, 180, 35, 'F');

        // Departure
        $pdf->SetXY(25, $y + 4);
        $pdf->SetFont('Helvetica', 'B', 18);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->Cell(60, 8, $this->pdfSafe($ticket['departure_city']), 0, 1, 'C');
        $pdf->SetX(25);
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetTextColor($r, $g, $b);
        $pdf->Cell(60, 5, date('d M Y', strtotime($ticket['departure_date'])), 0, 1, 'C');
        $pdf->SetX(25);
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->Cell(60, 5, date('h:i A', strtotime($ticket['departure_time'])), 0, 1, 'C');

        // Arrow
        $pdf->SetXY(90, $y + 8);
        $pdf->SetFont('Helvetica', 'B', 20);
        $pdf->SetTextColor($r, $g, $b);
        $pdf->Cell(30, 10, '>>>', 0, 0, 'C');
        $pdf->SetDrawColor($r, $g, $b);
        $pdf->Line(93, $y + 17, 117, $y + 17);

        // Arrival
        $pdf->SetXY(125, $y + 4);
        $pdf->SetFont('Helvetica', 'B', 18);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->Cell(60, 8, $this->pdfSafe($ticket['arrival_city']), 0, 1, 'C');
        $pdf->SetX(125);
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetTextColor($r, $g, $b);
        $pdf->Cell(60, 5, date('d M Y', strtotime($ticket['departure_date'])), 0, 1, 'C');
        $pdf->SetX(125);
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->Cell(60, 5, date('h:i A', strtotime($ticket['arrival_time'])), 0, 1, 'C');

        $pdf->SetY($y + 40);
    }

    private function pdfPricing($pdf, $ticket, $r, $g, $b) {
        $y = $pdf->GetY();
        $pdf->SetFillColor(238, 242, 255);
        $pdf->Rect(15, $y, 180, 25, 'F');

        $pdf->SetXY(20, $y + 3);
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(80, 6, 'Number of Passengers:', 0, 0, 'L');
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->Cell(85, 6, (string)$ticket['passenger_count'], 0, 1, 'R');

        $pdf->SetX(20);
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(80, 6, 'Price per Passenger:', 0, 0, 'L');
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->Cell(85, 6, 'Rs. ' . number_format($ticket['ticket_price'], 2), 0, 1, 'R');

        $pdf->Ln(2);

        // Total
        $totalY = $pdf->GetY();
        $pdf->SetFillColor($r, $g, $b);
        $pdf->Rect(15, $totalY, 180, 14, 'F');
        $pdf->SetXY(20, $totalY + 3);
        $pdf->SetFont('Helvetica', 'B', 13);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(80, 8, 'TOTAL AMOUNT:', 0, 0, 'L');
        $pdf->Cell(85, 8, 'Rs. ' . number_format($ticket['total_price'], 2), 0, 1, 'R');

        $pdf->SetY($totalY + 18);
    }
}
?>