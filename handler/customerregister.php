<?php
session_start();
require_once '../vendor/autoload.php';
include('../partials/connect.php');
include('../securimage-master/securimage.php');

use Denpa\Bitcoin\Client as BitcoinClient;

if (!isset($_POST['signup'])) {
    header('Location: ../customerforms.php');
    exit();
}

// Verify CAPTCHA
$securimage = new Securimage();
if (!$securimage->check($_POST['captcha_code'])) {
    $_SESSION['register_error'] = 'The security code entered was incorrect.';
    $_SESSION['register_data'] = $_POST;
    header('Location: ../customerforms.php');
    exit();
}

// Validate input
$email = trim($_POST['email']);
$password = $_POST['password'];
$password2 = $_POST['password2'];
$pin = trim($_POST['pin']);

// Validation checks
if (empty($email) || empty($password) || empty($pin)) {
    $_SESSION['register_error'] = 'All required fields must be filled';
    $_SESSION['register_data'] = $_POST;
    header('Location: ../customerforms.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['register_error'] = 'Please enter a valid email address';
    $_SESSION['register_data'] = $_POST;
    header('Location: ../customerforms.php');
    exit();
}

if ($password !== $password2) {
    $_SESSION['register_error'] = 'Passwords do not match';
    $_SESSION['register_data'] = $_POST;
    header('Location: ../customerforms.php');
    exit();
}

if (strlen($password) < 8) {
    $_SESSION['register_error'] = 'Password must be at least 8 characters long';
    $_SESSION['register_data'] = $_POST;
    header('Location: ../customerforms.php');
    exit();
}

if (!is_numeric($pin) || strlen($pin) < 4) {
    $_SESSION['register_error'] = 'PIN must be at least 4 digits';
    $_SESSION['register_data'] = $_POST;
    header('Location: ../customerforms.php');
    exit();
}

try {
    // Check if email already exists
    $stmt = $connect->prepare("SELECT id FROM customers WHERE username = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['register_error'] = 'Email address already registered';
        $_SESSION['register_data'] = $_POST;
        header('Location: ../customerforms.php');
        exit();
    }

    // Initialize Bitcoin client
    $bitcoind = new BitcoinClient([
        'scheme'   => 'http',
        'host'     => 'localhost',
        'port'     => 8332,
        'user'     => 'bitcoin',
        'password' => 'local321'
    ]);

    // Create or load wallet
    try {
        $wallet_response = $bitcoind->createwallet($email);
    } catch (Exception $e) {
        $bitcoind->loadwallet($email);
    }

    // Generate new address
    $address_response = $bitcoind->wallet($email)->getnewaddress();
    $btc_address = $address_response->get();

    // Hash password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Create referral code
    $referral_code = $email . rand(5, 15);

    // Begin transaction
    $connect->begin_transaction();

    // Insert customer
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

    if (!$stmt->execute()) {
        throw new Exception("Failed to create account");
    }

    // Commit transaction
    $connect->commit();

    // Clear any previous session data
    unset($_SESSION['register_error']);
    unset($_SESSION['register_data']);

    // Set success message
    $_SESSION['register_success'] = true;
    $_SESSION['btc_address'] = $btc_address;

    // Redirect to success page
    header('Location: ../registration_success.php');
    exit();

} catch (Exception $e) {
    // Rollback transaction if active
    if ($connect->inTransaction()) {
        $connect->rollback();
    }

    $_SESSION['register_error'] = 'Registration failed: ' . $e->getMessage();
    $_SESSION['register_data'] = $_POST;
    header('Location: ../customerforms.php');
    exit();
}