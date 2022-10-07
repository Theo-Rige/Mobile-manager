<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include_once $path . '/wp-load.php';

global $wpdb;
$params = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . 'mobile_manager' . " WHERE id = 1", ARRAY_A);
echo json_encode($params);
