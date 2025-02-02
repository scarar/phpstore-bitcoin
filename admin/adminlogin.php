<?php
session_start();
include("adminpartials/head.php");
include("../includes/Database.php");

if (isset($_SESSION['admin_id'])) {
    header('location: adminindex.php');
    exit();
}

$message = '';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Input validation
    if (empty($email) || empty($password)) {
        $message = '<div class="alert alert-warning">Please fill in all fields</div>';
    } else {
        try {
            $db = Database::getInstance();
            $stmt = $db->getPdo()->prepare("SELECT * FROM admins WHERE email = ? OR username = ? LIMIT 1");
            $stmt->execute([$email, $email]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                header('location: adminindex.php');
                exit();
            } else {
                $message = '<div class="alert alert-danger">Invalid credentials</div>';
            }
        } catch (Exception $e) {
            $message = '<div class="alert alert-danger">Login error: ' . $e->getMessage() . '</div>';
        }
    }
}
?>

<div class="login-box">
    <div class="login-logo">
        <a href="../index.php"><b>PHP</b>Store</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>
        <?php echo $message; ?>

        <form action="adminlogin.php" method="POST">
            <div class="form-group has-feedback">
                <input type="text" class="form-control" placeholder="Email or Username" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Password" name="password">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox"> Remember Me
                        </label>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-xs-4">
                    <button type="submit" name="login" class="btn btn-primary btn-block btn-flat">Sign In</button>
                </div>
                <!-- /.col -->
            </div>
        </form>

        <a href="#">I forgot my password</a><br>

    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="plugins/iCheck/icheck.min.js"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' /* optional */
        });
    });
</script>