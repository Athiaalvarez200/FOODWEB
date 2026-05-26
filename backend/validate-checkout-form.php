<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../checkout.php");
    exit();
}

if (!isset($_POST['name']) || !isset($_POST['phone']) || !isset($_POST['address'])) {
    header("Location: ../checkout.php");
    exit();
}

$name = trim($_POST['name']);
$phone = trim($_POST['phone']);
$address = trim($_POST['address']);

if ($name == "" || $phone == "" || $address == "") {
    header("Location: ../checkout.php");
    exit();
}

$_SESSION['checkout_data'] = $_POST;

header("Location: ../confirm-order.php");
exit();
?>