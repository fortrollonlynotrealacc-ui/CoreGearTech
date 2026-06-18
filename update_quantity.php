<?php
session_start();
include '../Project in WST/server/server.php';

$sql = "SELECT purchase_id, guest_id, product_id, quantity, purchase_date, price FROM cgt_products";
$sqlguest = "SELECT purchase_id, guest_id, product_id, quantity, purchase_date, price FROM cgt_guest_purchases";
$sqluser = "SELECT purchase_id, user_id, product_id, quantity, purchase_date, price FROM cgt_guest_purchases";





?>