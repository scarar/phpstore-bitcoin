<?php
session_start();
include("handler/customersession.php");
include("partials/head.php");

if (!isset($_SESSION['bitcoin_payment'])) {
    header("Location: cart.php");
    exit();
}

$payment = $_SESSION['bitcoin_payment'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bitcoin Payment - PHP Store</title>
    <?php include("partials/head.php"); ?>
    <style>
        .bitcoin-payment {
            text-align: center;
            padding: 2rem;
            margin: 2rem auto;
            max-width: 600px;
        }
        .qr-code {
            margin: 2rem auto;
            padding: 1rem;
            background: white;
            display: inline-block;
        }
        .payment-details {
            margin: 2rem 0;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .payment-status {
            margin: 1rem 0;
            padding: 1rem;
            border-radius: 5px;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-completed {
            background: #d4edda;
            color: #155724;
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
            <a href="cart.php" class="stext-109 cl8 hov-cl1 trans-04">
                Cart
                <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
            </a>
            <span class="stext-109 cl4">
                Bitcoin Payment
            </span>
        </div>

        <div class="bitcoin-payment">
            <h2 class="mtext-109 cl2 p-b-30">Bitcoin Payment</h2>

            <div class="payment-details">
                <p>Please send exactly <strong><?php echo number_format($payment['amount_btc'], 8); ?> BTC</strong> to the following address:</p>
                <div class="qr-code">
                    <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=bitcoin:<?php echo $payment['bitcoin_address']; ?>?amount=<?php echo $payment['amount_btc']; ?>" alt="Bitcoin QR Code">
                </div>
                <p class="address-text">
                    <strong>Address:</strong><br>
                    <span class="monospace"><?php echo $payment['bitcoin_address']; ?></span>
                </p>
            </div>

            <div id="payment-status" class="payment-status status-pending">
                <h4>Payment Status: <span id="status-text">Pending</span></h4>
                <p>Waiting for payment confirmation...</p>
            </div>

            <p class="help-text">
                The payment will be automatically confirmed once we receive it. Please do not close this page.
            </p>
        </div>
    </div>

    <?php include('partials/footer.php'); ?>

    <script>
        function checkPaymentStatus() {
            fetch('bitcoin_payment.php?check_status=1&payment_id=<?php echo $payment['payment_id']; ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const status = data.data;
                        const statusDiv = document.getElementById('payment-status');
                        const statusText = document.getElementById('status-text');

                        if (status.status === 'completed') {
                            statusDiv.className = 'payment-status status-completed';
                            statusText.textContent = 'Completed';
                            statusDiv.innerHTML = '<h4>Payment Status: Completed</h4><p>Thank you for your payment!</p>';
                            // Redirect to success page after 3 seconds
                            setTimeout(() => {
                                window.location.href = 'order_success.php';
                            }, 3000);
                        } else {
                            // Continue checking if payment is still pending
                            setTimeout(checkPaymentStatus, 30000); // Check every 30 seconds
                        }
                    }
                })
                .catch(error => {
                    console.error('Error checking payment status:', error);
                    setTimeout(checkPaymentStatus, 30000); // Retry on error
                });
        }

        // Start checking payment status
        checkPaymentStatus();
    </script>
</body>
</html>