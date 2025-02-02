<!DOCTYPE html>
<html lang="en">
<?php
include ("partials/head.php");
?>
<body class="animsition">
        <?php
        include ("partials/header.php");


?>

        <!-- Title page -->
        <section class="bg-img1 txt-center p-lr-15 p-tb-92" style="background-image: url('images/about1.jpg');">
                <h2 class="ltext-105 cl0 txt-center">
                Customers
                </h2>
        </section>      


        <!-- Content page -->
        <section class="bg0 p-t-104 p-b-116">
                <div class="container">
                        <div class="flex-w flex-tr">
                                <div class="size-210 bor10 p-lr-70 p-t-55 p-b-70 p-lr-15-lg w-full-md">
                                        <form action="handler/customerlogin.php" method="POST">
                                                <h4 class="mtext-105 cl2 txt-center p-b-30">
                                                        Log in
                                                </h4>

                                                <div class="bor8 m-b-20 how-pos4-parent">
                                                        <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="text" name="email" placeholder="Your Email Address">
                                                        <img class="how-pos4 pointer-none" src="images/icons/icon-email.png" alt="ICON">
                                                </div>

                                                <div class="bor8 m-b-30">
                                                        <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="password" name="password" placeholder="password">
                                                </div>

                                                <div class="bor8 m-b-30">
                                                        <div class="captcha-container" style="text-align: center; margin-bottom: 10px;">
                                                            <img id="captcha" src="securimage-master/securimage_show.php" alt="CAPTCHA Image" style="border: 1px solid #ccc; margin-bottom: 10px;" />
                                                            <div class="captcha-buttons" style="margin-top: 5px;">
                                                                <a href="#" onclick="document.getElementById('captcha').src = 'securimage-master/securimage_show.php?' + Math.random(); return false" style="margin-right: 10px;">
                                                                    <img src="securimage-master/images/refresh.png" alt="Refresh Image" onclick="this.blur()" style="vertical-align: middle;" />
                                                                </a>
                                                                <a href="securimage-master/securimage_play.php">
                                                                    <img src="securimage-master/images/audio_icon.png" alt="Play Audio" style="vertical-align: middle;" />
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="text" name="captcha_code" placeholder="Enter Captcha Code" maxlength="6">
                                                </div>

                                                <button class="flex-c-m stext-101 cl0 size-121 bg3 bor1 hov-btn3 p-lr-15 trans-04 pointer" name="login">
                                                        Log in
                                                </button>
                                        </form>
                                </div>

                                <div class="size-210 bor10 p-lr-70 p-t-55 p-b-70 p-lr-15-lg w-full-md">
                                        <form action="handler/customerregister.php" method="POST">
                                                <h4 class="mtext-105 cl2 txt-center p-b-30">
                                                        Register
                                                </h4>

                                                <div class="bor8 m-b-20 how-pos4-parent">
                                                        <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="text" name="email" placeholder="Your Email Address">
                                                        <img class="how-pos4 pointer-none" src="images/icons/icon-email.png" alt="ICON">
                                                </div>

                                                <div class="bor8 m-b-30">
                                                        <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="password" name="password" placeholder="password">
                                                </div>
                                                <div class="bor8 m-b-30">
                                                        <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="password" name="password2" placeholder="Confirm Password">
                                                </div>

                                                <div class="bor8 m-b-30">
                                                        <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="text" name="pin" placeholder="PIN Code (numbers only)">
                                                </div>

                                                <div class="bor8 m-b-30">
                                                        <div class="captcha-container" style="text-align: center; margin-bottom: 10px;">
                                                            <img id="captcha2" src="securimage-master/securimage_show.php" alt="CAPTCHA Image" style="border: 1px solid #ccc; margin-bottom: 10px;" />
                                                            <div class="captcha-buttons" style="margin-top: 5px;">
                                                                <a href="#" onclick="document.getElementById('captcha2').src = 'securimage-master/securimage_show.php?' + Math.random(); return false" style="margin-right: 10px;">
                                                                    <img src="securimage-master/images/refresh.png" alt="Refresh Image" onclick="this.blur()" style="vertical-align: middle;" />
                                                                </a>
                                                                <a href="securimage-master/securimage_play.php">
                                                                    <img src="securimage-master/images/audio_icon.png" alt="Play Audio" style="vertical-align: middle;" />
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="text" name="captcha_code" placeholder="Enter Captcha Code" maxlength="6">
                                                </div>

                                                <button class="flex-c-m stext-101 cl0 size-121 bg3 bor1 hov-btn3 p-lr-15 trans-04 pointer" name="signup">
                                                        Register
                                                </button>
                                        </form>
                                </div>

                        </div>
                </div>
        </section>      
        
        
        <!-- Map -->
        <div class="map">
                <div class="size-303" id="google_map" data-map-x="40.691446" data-map-y="-73.886787" data-pin="images/icons/pin.png" data-scrollwhell="0" data-draggable="1" data-zoom="11"></div>
        </div>



        <!-- Footer -->
        <?php
        include('partials/footer.php');
        ?>

</body>
</html>