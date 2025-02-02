<?php
session_start();
if (!isset($_SESSION['register_success'])) {
    header('Location: customerforms.php');
    exit();
}

$btc_address = $_SESSION['btc_address'];

// Clear session data
unset($_SESSION['register_success']);
unset($_SESSION['btc_address']);

include("partials/head.php");
?>
<body class="animsition">
    <?php include("partials/header.php"); ?>

    <section class="bg-img1 txt-center p-lr-15 p-tb-92" style="background-image: url('images/bg-01.jpg');">
        <h2 class="ltext-105 cl0 txt-center">Registration Successful</h2>
    </section>

    <section class="bg0 p-t-75 p-b-120">
        <div class="container">
            <div class="row p-b-148">
                <div class="col-md-8 col-lg-6 m-lr-auto">
                    <div class="bor10 p-lr-40 p-t-30 p-b-40 m-l-63 m-r-40 m-lr-0-xl p-lr-15-sm">
                        <h4 class="mtext-111 cl2 p-b-16">
                            Welcome to PHP Store!
                        </h4>

                        <p class="stext-113 cl6 p-b-26">
                            Your account has been created successfully. You can now log in using your email and password.
                        </p>

                        <div class="bor8 m-b-20 how-pos4-parent">
                            <div class="alert alert-info">
                                <strong>Your Bitcoin Address:</strong><br>
                                <span class="monospace" id="btc-address"><?php echo htmlspecialchars($btc_address); ?></span>
                                <button class="btn btn-sm btn-outline-secondary ml-2" onclick="copyAddress()">
                                    <i class="fa fa-copy"></i> Copy
                                </button>
                            </div>
                        </div>

                        <p class="stext-113 cl6 p-b-26">
                            Please save your Bitcoin address. You will need it to receive payments.
                        </p>

                        <a href="customerforms.php" class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer">
                            Continue to Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include("partials/footer.php"); ?>

    <script>
        function copyAddress() {
            const address = document.getElementById('btc-address').textContent;
            navigator.clipboard.writeText(address).then(() => {
                alert('Bitcoin address copied to clipboard!');
            });
        }
    </script>
</body>
</html>