<?php
session_start();
include('../partials/connect.php');
include('../securimage-master/securimage.php');

if (isset($_POST['login'])) {
    // Verify CAPTCHA
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

    // Input validation
    if (empty($email) || empty($password)) {
        echo "<script>
            alert('Please fill in all fields');
            window.location.href='../customerforms.php';
        </script>";
        exit();
    }

    // Use prepared statement to prevent SQL injection
    $stmt = $connect->prepare("SELECT * FROM customers WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password using password_verify
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['customerid'] = $user['id'];
            $_SESSION['customername'] = $user['username'];
            
            // Redirect to cart
            header('location: ../cart.php');
            exit();
        } else {
            echo "<script>
                alert('Invalid username or password');
                window.location.href='../customerforms.php';
            </script>";
        }
    } else {
        echo "<script>
            alert('Invalid username or password');
            window.location.href='../customerforms.php';
        </script>";
    }
    
    $stmt->close();
}
?>