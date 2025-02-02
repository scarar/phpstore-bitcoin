<?php
session_start();
include("handler/customersession.php");
include("partials/head.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order Success - PHP Store</title>
    <?php include("partials/head.php"); ?>
    <style>
        .success-message {
            text-align: center;
            padding: 4rem 2rem;
            margin: 2rem auto;
            max-width: 600px;
        }
        .success-icon {
            color: #28a745;
            font-size: 4rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body class="animsition">
    <?php include("partials/header.php"); ?>

    <div class="container">
        <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
            <a href="index.php" class="stext-109 cl8 hov-cl1 trans-04">
                Home
                <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
            </a>
            <span class="stext-109 cl4">
                Order Success
            </span>
        </div>

        <div class="success-message">
            <div class="success-icon">
                <i class="fa fa-check-circle"></i>
            </div>
            <h2 class="mtext-109 cl2 p-b-30">Order Completed Successfully!</h2>
            <p>Thank you for your purchase. Your order has been successfully processed.</p>
            <p>You will receive a confirmation email shortly.</p>
            <div class="mt-4">
                <a href="index.php" class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>

    <?php include('partials/footer.php'); ?>
</body>
</html>