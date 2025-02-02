<!--===============================================================================================-->  
        <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
        <script src="vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
        <script src="vendor/bootstrap/js/popper.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
        <script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
        <script src="vendor/daterangepicker/moment.min.js"></script>
        <script src="vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
        <script src="vendor/sweetalert/sweetalert.min.js"></script>
<!--===============================================================================================-->
        <script>
            // CAPTCHA refresh
            function refreshCaptcha(id) {
                var img = document.getElementById(id);
                img.src = 'securimage-master/securimage_show.php?' + Math.random();
            }

            // Form validation
            document.addEventListener('DOMContentLoaded', function() {
                // Registration form validation
                var regForm = document.querySelector('form[action="handler/customerregister.php"]');
                if (regForm) {
                    regForm.addEventListener('submit', function(e) {
                        var password = regForm.querySelector('input[name="password"]').value;
                        var password2 = regForm.querySelector('input[name="password2"]').value;
                        var pin = regForm.querySelector('input[name="pin"]').value;
                        var email = regForm.querySelector('input[name="email"]').value;
                        var captcha = regForm.querySelector('input[name="captcha_code"]').value;

                        if (!email || !password || !password2 || !pin || !captcha) {
                            e.preventDefault();
                            swal('Error', 'Please fill in all fields', 'error');
                            return false;
                        }

                        if (password !== password2) {
                            e.preventDefault();
                            swal('Error', 'Passwords do not match', 'error');
                            return false;
                        }

                        if (password.length < 8) {
                            e.preventDefault();
                            swal('Error', 'Password must be at least 8 characters long', 'error');
                            return false;
                        }

                        if (!pin.match(/^\d{4,}$/)) {
                            e.preventDefault();
                            swal('Error', 'PIN must be at least 4 digits', 'error');
                            return false;
                        }
                    });
                }

                // Login form validation
                var loginForm = document.querySelector('form[action="handler/customerlogin.php"]');
                if (loginForm) {
                    loginForm.addEventListener('submit', function(e) {
                        var email = loginForm.querySelector('input[name="email"]').value;
                        var password = loginForm.querySelector('input[name="password"]').value;
                        var captcha = loginForm.querySelector('input[name="captcha_code"]').value;

                        if (!email || !password || !captcha) {
                            e.preventDefault();
                            swal('Error', 'Please fill in all fields', 'error');
                            return false;
                        }
                    });
                }

                // Show messages
                <?php if (isset($_SESSION['register_error'])): ?>
                    swal('Error', <?php echo json_encode($_SESSION['register_error']); ?>, 'error');
                <?php endif; ?>

                <?php if (isset($_SESSION['login_error'])): ?>
                    swal('Error', <?php echo json_encode($_SESSION['login_error']); ?>, 'error');
                <?php endif; ?>
            });
        </script>