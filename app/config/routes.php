<?php
/**
 * Application Routes Configuration
 * Format: $routes['route_name'] = array('module'=>'...', 'controller'=>'...', 'method'=>'...');
 */
$routes = array();

// Default & Auth Routes
$routes['default'] = array('module'=>'auth', 'controller'=>'AuthController', 'method'=>'login');
$routes['login']   = array('module'=>'auth', 'controller'=>'AuthController', 'method'=>'login');
$routes['logout']  = array('module'=>'auth', 'controller'=>'AuthController', 'method'=>'logout');
$routes['reset']   = array('module'=>'auth', 'controller'=>'AuthController', 'method'=>'reset');

// Ticket Routes
$routes['dashboard']   = array('module'=>'ticket', 'controller'=>'TicketController', 'method'=>'dashboard');
$routes['list_tickets']= array('module'=>'ticket', 'controller'=>'TicketController', 'method'=>'list');
$routes['add_ticket']  = array('module'=>'ticket', 'controller'=>'TicketController', 'method'=>'add');
$routes['edit_ticket'] = array('module'=>'ticket', 'controller'=>'TicketController', 'method'=>'edit');
$routes['view_ticket'] = array('module'=>'ticket', 'controller'=>'TicketController', 'method'=>'view');
$routes['delete_ticket'] = array('module'=>'ticket', 'controller'=>'TicketController', 'method'=>'delete');
$routes['download_pdf']  = array('module'=>'ticket', 'controller'=>'TicketController', 'method'=>'pdf');
$routes['export_excel']  = array('module'=>'ticket', 'controller'=>'TicketController', 'method'=>'exportExcel');
$routes['ajax_add_airport'] = array('module'=>'ticket', 'controller'=>'TicketController', 'method'=>'ajax_add_airport');
$routes['ajax_add_airline'] = array('module'=>'ticket', 'controller'=>'TicketController', 'method'=>'ajax_add_airline');

return $routes;
?>
