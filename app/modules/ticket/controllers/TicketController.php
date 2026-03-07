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
// Include FPDF
require_once FPDF_PATH;

class ST_PDF extends FPDF {
    function RoundedRect($x, $y, $w, $h, $r, $style = '', $corners = '1234') {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F') $op='f';
        elseif($style=='FD' || $style=='DF') $op='B';
        else $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k));
        $xc = $x+$w-$r; $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k, ($hp-$y)*$k));
        if (strpos($corners, '2')===false) $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k, ($hp-$y)*$k));
        else $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
        $xc = $x+$w-$r; $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k, ($hp-$yc)*$k));
        if (strpos($corners, '3')===false) $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k, ($hp-($y+$h))*$k));
        else $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x+$r; $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k, ($hp-($y+$h))*$k));
        if (strpos($corners, '4')===false) $this->_out(sprintf('%.2F %.2F l', ($x)*$k, ($hp-($y+$h))*$k));
        else $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
        $xc = $x+$r; $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', ($x)*$k, ($hp-$yc)*$k));
        if (strpos($corners, '1')===false) {
            $this->_out(sprintf('%.2F %.2F l', ($x)*$k, ($hp-$y)*$k));
            $this->_out(sprintf('%.2F %.2F l', ($x+$r)*$k, ($hp-$y)*$k));
        } else $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }
    function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
            $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }
}

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
        // Just recent 5 tickets for dashboard
        $recent_tickets = array_slice($this->ticketModel->getUserTickets($userId), 0, 5);
        $stats = $this->ticketModel->getStats($userId);

        // Chart data preparation - tickets confirmed vs pending by last 6 months
        $chartData = $this->ticketModel->getMonthlyTicketStats($userId);

        $this->render('tickets/dashboard', [
            'tickets'        => $recent_tickets,
            'total_tickets'  => $stats['total'],
            'confirmed_count'=> $stats['confirmed'],
            'pending_count'  => $stats['pending'],
            'total_revenue'  => $stats['revenue'],
            'chart_data'     => json_encode($chartData),
            'username'       => $this->getUsername(),
            'success'        => $this->getFlash('success'),
            'error'          => $this->getFlash('error'),
            'page_title'     => 'Dashboard'
        ]);
    }

    /**
     * List all tickets with Pagination and Search
     * Route: /ticket/list_tickets
     */
    public function list() {
        $userId = $this->getUserId();
        
        $search = $this->getData('search') ?? '';
        $page = (int)($this->getData('page') ?? 1);
        if ($page < 1) $page = 1;
        $limit = 10;
        
        $result = $this->ticketModel->getPaginatedUserTickets($userId, $page, $limit, $search);

        $available_columns = [
            'booking_reference' => 'Booking Ref',
            'client_name'       => 'Client Name',
            'client_email'      => 'Client Email',
            'client_phone'      => 'Client Phone',
            'route'             => 'Route',
            'departure_date'    => 'Departure Date',
            'departure_time'    => 'Departure Time',
            'arrival_time'      => 'Arrival Time',
            'airline_name'      => 'Airline Name',
            'flight_number'     => 'Flight Number',
            'passenger_count'   => 'Passengers',
            'ticket_price'      => 'Ticket Price',
            'total_price'       => 'Total Price',
            'status'            => 'Status',
            'notes'             => 'Notes'
        ];

        $default_columns = ['booking_reference', 'client_name', 'route', 'departure_date', 'total_price', 'status'];
        
        $selected_columns = $default_columns;
        if (isset($_COOKIE['ticket_columns'])) {
            $parsed_cols = json_decode($_COOKIE['ticket_columns'], true);
            if (is_array($parsed_cols) && count($parsed_cols) > 0) {
                $valid_cols = array_intersect($parsed_cols, array_keys($available_columns));
                if (count($valid_cols) > 0) {
                    $selected_columns = array_values($valid_cols);
                }
            }
        }

        $this->render('tickets/list', [
            'tickets'            => $result['data'],
            'current_page'       => $page,
            'total_pages'        => $result['total_pages'],
            'search'             => $search,
            'total_results'      => $result['total_records'],
            'limit'              => $limit,
            'available_columns'  => $available_columns,
            'selected_columns'   => $selected_columns,
            'success'            => $this->getFlash('success'),
            'error'              => $this->getFlash('error'),
            'page_title'         => 'All Tickets'
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
                    $this->redirect('list_tickets');
                } else {
                    $error = 'Error creating ticket. Please try again.';
                }
            }
        }

        $airports = $this->ticketModel->getAirports();
        $airlines = $this->ticketModel->getAirlines();

        $this->render('tickets/add', [
            'error'      => $error,
            'success'    => $success,
            'airports'   => $airports,
            'airlines'   => $airlines,
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
            $this->redirect('dashboard');
        }

        $userId = $this->getUserId();
        $ticket = $this->ticketModel->getTicketForUser($ticketId, $userId);

        if (!$ticket) {
            $this->setFlash('error', 'Ticket not found');
            $this->redirect('dashboard');
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

        $airports = $this->ticketModel->getAirports();
        $airlines = $this->ticketModel->getAirlines();

        $this->render('tickets/edit', [
            'ticket'     => $ticket,
            'ticket_id'  => $ticketId,
            'error'      => $error,
            'success'    => $success,
            'airports'   => $airports,
            'airlines'   => $airlines,
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
            $this->redirect('dashboard');
        }

        $userId = $this->getUserId();
        $ticket = $this->ticketModel->getTicketForUser($ticketId, $userId);

        if (!$ticket) {
            $this->setFlash('error', 'Ticket not found');
            $this->redirect('dashboard');
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
            $this->redirect('dashboard');
        }

        $userId = $this->getUserId();
        if ($this->ticketModel->deleteTicket($ticketId, $userId)) {
            $this->setFlash('success', 'Ticket deleted successfully!');
        } else {
            $this->setFlash('error', 'Error deleting ticket');
        }

        $this->redirect('list_tickets');
    }

    /**
     * Download ticket as PDF (real FPDF)
     * Route: /ticket/pdf/{id}
     */
    public function pdf($id = null) {
        $ticketId = (int)($id ?? $this->getData('id'));
        if (!$ticketId) {
            $this->redirect('dashboard');
        }

        $userId = $this->getUserId();
        $ticket = $this->ticketModel->getTicketForUser($ticketId, $userId);

        if (!$ticket) {
            die('Ticket not found');
        }

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
        $pdf = new ST_PDF();
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        // Professional Blue Palette
        $blue = [0, 93, 166];      // EaseMyTrip Blue
        $gray_text = [100, 100, 100];
        $line_color = [230, 230, 230];

        // ── TOP HEADER ──
        // Logo Placeholder Area
        $pdf->SetDrawColor($line_color[0], $line_color[1], $line_color[2]);
        $pdf->Rect(10, 10, 190, 25);
        $pdf->Line(100, 10, 100, 35);

        // Company Brand
        $pdf->SetXY(15, 15);
        $pdf->SetFont('Helvetica', 'B', 20);
        $pdf->SetTextColor($blue[0], $blue[1], $blue[2]);
        $pdf->Cell(50, 10, 'Santhosh Travels', 0, 0, 'L');
        $pdf->SetFont('Helvetica', 'I', 8);
        $pdf->Cell(30, 10, '.in', 0, 0, 'L');

        // Booking Info (Right Side)
        $pdf->SetXY(105, 12);
        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->SetTextColor($blue[0], $blue[1], $blue[2]);
        $pdf->Cell(85, 4, 'Booking ID - ' . $ticket['booking_reference'], 0, 2, 'R');
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->SetTextColor($gray_text[0], $gray_text[1], $gray_text[2]);
        $pdf->Cell(85, 4, 'Booking Date - ' . date('D, d M Y', strtotime($ticket['created_at'])), 0, 2, 'R');
        $pdf->Cell(85, 4, 'Booking Status - ' . strtoupper($ticket['status']), 0, 2, 'R');

        $pdf->SetY(40);

        // ── OUTBOUND FLIGHT DETAILS ──
        $pdf->SetFillColor(245, 248, 255);
        $pdf->SetTextColor($blue[0], $blue[1], $blue[2]);
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->Cell(190, 8, '  Outbound Flight Details', 0, 1, 'L', true);
        
        $pdf->SetDrawColor($line_color[0], $line_color[1], $line_color[2]);
        $pdf->Rect(10, 40, 190, 35); // Outbound box

        $pdf->SetY(50);
        // Flight Row
        $pdf->SetX(15);
        // Airline Icon Placeholder - Using a clean flight icon
        $pdf->SetFillColor(245, 248, 255);
        $pdf->SetDrawColor($blue[0], $blue[1], $blue[2]);
        $pdf->RoundedRect(15, 52, 10, 10, 1, 'DF');
        $pdf->SetXY(15, 52);
        $pdf->SetFont('ZapfDingbats', '', 14);
        $pdf->SetTextColor($blue[0], $blue[1], $blue[2]);
        $pdf->Cell(10, 10, 'Q', 0, 0, 'C'); // Airplane symbol
        
        $pdf->SetXY(28, 52);
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->Cell(40, 5, $this->pdfSafe($ticket['airline_name']), 0, 2, 'L');
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->SetTextColor($gray_text[0], $gray_text[1], $gray_text[2]);
        $pdf->Cell(40, 4, $ticket['flight_number'], 0, 1, 'L');

        // From
        $pdf->SetXY(70, 52);
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->Cell(45, 5, $this->pdfSafe($ticket['departure_city']), 0, 2, 'L');
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->SetTextColor($gray_text[0], $gray_text[1], $gray_text[2]);
        $pdf->Cell(45, 4, date('D, d M Y', strtotime($ticket['departure_date'])), 0, 1, 'L');

        // Arrow/Duration placeholder
        $pdf->SetXY(115, 54);
        $pdf->SetFont('Helvetica', '', 7);
        $pdf->Cell(20, 5, '----->', 0, 0, 'C');

        // To
        $pdf->SetXY(135, 52);
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->Cell(45, 5, $this->pdfSafe($ticket['arrival_city']), 0, 2, 'R');
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->SetTextColor($gray_text[0], $gray_text[1], $gray_text[2]);
        $pdf->Cell(45, 4, date('h:i A', strtotime($ticket['arrival_time'])), 0, 1, 'R');

        $pdf->SetY(80);

        // ── PASSENGER DETAILS TABLE ──
        $pdf->SetFillColor(245, 248, 255);
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->SetTextColor(50, 50, 50);
        $pdf->Cell(10, 8, 'SNo', 1, 0, 'C', true);
        $pdf->Cell(60, 8, 'Passenger Name', 1, 0, 'L', true);
        $pdf->Cell(25, 8, 'Status', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Airline PNR', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'Ticket Number', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Total Fare', 1, 1, 'R', true);

        $pdf->SetFont('Helvetica', '', 8);
        $pdf->Cell(10, 10, '1', 1, 0, 'C');
        $pdf->Cell(60, 10, ' ' . $this->pdfSafe($ticket['client_name']), 1, 0, 'L');
        $pdf->Cell(25, 10, ucfirst($ticket['status']), 1, 0, 'C');
        $pdf->Cell(30, 10, 'Confirmed', 1, 0, 'C');
        $pdf->Cell(35, 10, 'ST' . date('Ymd', strtotime($ticket['created_at'])) . $ticket['id'], 1, 0, 'C');
        $pdf->Cell(30, 10, number_format($ticket['total_price'], 2) . ' ', 1, 1, 'R');

        $pdf->Ln(5);

        // ── FARE DETAILS (Stacked Layout) ──
        $pdf->SetX(10);
        $pdf->SetFillColor(245, 248, 255);
        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->SetTextColor($blue[0], $blue[1], $blue[2]);
        $pdf->Cell(190, 8, '  Fare Details', 0, 1, 'L', true);

        $pdf->SetFont('Helvetica', '', 8);
        $pdf->SetTextColor(50, 50, 50);
        // Table frame
        $fy = $pdf->GetY();
        $pdf->Rect(10, $fy, 190, 32); 
        $pdf->Line(10, $fy+8, 200, $fy+8);
        $pdf->Line(10, $fy+16, 200, $fy+16);
        $pdf->Line(10, $fy+24, 200, $fy+24);
        $pdf->Line(100, $fy, 100, $fy+32);

        $pdf->SetXY(15, $fy + 1.5);
        $pdf->Cell(80, 5, 'Basic Fare', 0, 0, 'L');
        $pdf->SetX(105);
        $pdf->Cell(85, 5, number_format($ticket['total_price'], 2), 0, 1, 'R');

        $pdf->SetXY(15, $fy + 9.5);
        $pdf->Cell(80, 5, 'Taxes & Other Charges', 0, 0, 'L');
        $pdf->SetX(105);
        $pdf->Cell(85, 5, 'Included', 0, 1, 'R');

        $pdf->SetXY(15, $fy + 17.5);
        $pdf->Cell(80, 5, 'Discount', 0, 0, 'L');
        $pdf->SetX(105);
        $pdf->Cell(85, 5, '0.00', 0, 1, 'R');

        $pdf->SetXY(15, $fy + 24.5);
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(80, 8, 'Total Fare', 0, 0, 'L');
        $pdf->SetX(105);
        $pdf->Cell(85, 8, 'Rs. ' . number_format($ticket['total_price'], 2), 0, 1, 'R');

        $pdf->Ln(10);

        // ── TERMS & CONDITIONS ──
        $pdf->SetX(10);
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->Cell(190, 5, 'Terms & Conditions / Cancellation Policy', 0, 1, 'L');
        $pdf->SetFont('Helvetica', '', 7);
        $pdf->SetTextColor($gray_text[0], $gray_text[1], $gray_text[2]);
        $pdf->MultiCell(185, 4, "- Cancellation charges are applicable as per airline policy.\n- Please report at the airport at least 2 hours before departure.\n- Carry a valid photo ID and this e-ticket during travel.\n- Santhosh Travels is not responsible for any flight delays or cancellations.", 0, 'L');

        // ── FINAL FOOTER ──
        $pdf->SetY(275);
        $pdf->SetFont('Helvetica', '', 7);
        $pdf->SetTextColor($gray_text[0], $gray_text[1], $gray_text[2]);
        $pdf->Cell(0, 4, 'Santhosh Air Travels - Professional Ticketing Solutions', 0, 1, 'C');
        $pdf->Cell(0, 4, 'Page 1 of 1', 0, 0, 'R');

        return $pdf;
    }



    private function pdfSafe($text) {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $text);
    }



    /**
     * Export all user tickets to Excel (CSV format)
     * Route: /ticket/export_excel
     */
    public function exportExcel() {
        $userId = $this->getUserId();
        if (!$userId) $this->redirect('login');

        $tickets = $this->ticketModel->getAllUserTicketsForExport($userId);

        $filename = "SanthoshTravels_Export_" . date('Ymd_His') . ".csv";

        // Headers format for Excel download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Output stream
        $output = fopen('php://output', 'w');
        
        // UTF-8 BOM for Excel to read chars properly
        fputs($output, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

        if (!empty($tickets)) {
            // Capitalize headers from DB column names
            $headers = array_keys($tickets[0]);
            $formattedHeaders = array_map(function($header) {
                return ucwords(str_replace('_', ' ', $header));
            }, $headers);
            
            fputcsv($output, $formattedHeaders);
            
            // Output Data
            foreach ($tickets as $row) {
                fputcsv($output, $row);
            }
        } else {
            fputcsv($output, ['No tickets found']);
        }
        
        fclose($output);
        exit;
    }
    /**
     * AJAX add airport
     */
    public function ajax_add_airport() {
        $city = $this->sanitize($_POST['city_name'] ?? '');
        $iata = strtoupper($this->sanitize($_POST['iata_code'] ?? ''));
        
        if (!empty($city) && !empty($iata)) {
            if ($this->ticketModel->createAirport($city, $iata)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            }
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => false]);
        exit;
    }

    /**
     * AJAX add airline
     */
    public function ajax_add_airline() {
        $name = $this->sanitize($_POST['name'] ?? '');
        
        if (!empty($name)) {
            if ($this->ticketModel->createAirline($name)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            }
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => false]);
        exit;
    }
}
?>