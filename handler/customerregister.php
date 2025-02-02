<?php
require_once '../vendor/autoload.php';
include('../partials/connect.php');
include('../securimage-master/securimage.php');

use Denpa\Bitcoin\Client as BitcoinClient;

$message = '';

if (isset($_POST['signup'])) {
    $securimage = new Securimage();
    if ($securimage->check($_POST['captcha_code']) == false) {
        echo "<script>
            alert('The security code entered was incorrect.');
            window.location.href='../customerforms.php';
        </script>";
        exit();
    }

    $email = $_POST['email'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $pin = $_POST['pin'];

    if ($email == '' || $password == '' || $pin == '') {
        echo "<script>
            alert('All required fields must be filled');
            window.location.href='../customerforms.php';
        </script>";
        exit();
    }

    if ($password !== $password2) {
        echo "<script>
            alert('Passwords do not match');
            window.location.href='../customerforms.php';
        </script>";
        exit();
    }

    if (!is_numeric($pin)) {
        echo "<script>
            alert('PIN code must be numeric');
            window.location.href='../customerforms.php';
        </script>";
        exit();
    }

    try {
        // Initialize Bitcoin client
        $bitcoind = new BitcoinClient([
            'scheme'   => 'http',
            'host'     => 'localhost',
            'port'     => 8332,
            'user'     => 'bitcoin',
            'password' => 'local321'
        ]);

        // Create wallet and get new address
        try {
            $wallet_response = $bitcoind->createwallet($email);
        } catch (Exception $e) {
            // Wallet might already exist, try to load it
            $bitcoind->loadwallet($email);
        }

        // Generate new address
        $address_response = $bitcoind->wallet($email)->getnewaddress();
        $btc_address = $address_response->get();

        // Hash password
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Create referral code
        $referral_code = $email . rand(5, 15);

        // Prepare and execute the SQL with proper escaping
        $stmt = $connect->prepare("
            INSERT INTO customers (
                username, password, pin, btc_address, 
                referral_code, balance, created_at
            ) VALUES (?, ?, ?, ?, ?, 0, NOW())
        ");

        $stmt->bind_param(
            "sssss",
            $email,
            $password_hash,
            $pin,
            $btc_address,
            $referral_code
        );

        if ($stmt->execute()) {
            echo "<script>
                alert('Registration successful! Your Bitcoin address: " . $btc_address . "');
                window.location.href='../customerforms.php';
            </script>";
        } else {
            throw new Exception("Database error: " . $stmt->error);
        }

    } catch (Exception $e) {
        echo "<script>
            alert('Registration failed: " . addslashes($e->getMessage()) . "');
            window.location.href='../customerforms.php';
        </script>";
    }
} else {
    header('Location: ../customerforms.php');
    exit();
}