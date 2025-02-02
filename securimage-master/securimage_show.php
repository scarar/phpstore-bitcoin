<?php

// Error handler function
function customError($errno, $errstr) {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: text/plain');
    echo "Error: $errstr";
    exit();
}

// Set error handler
set_error_handler("customError");

// Include the securimage class
require_once dirname(__FILE__) . '/securimage.php';

$img = new Securimage();

// Set some options
$img->image_width = 250;
$img->image_height = 80;
$img->perturbation = 0.85;
$img->image_bg_color = new Securimage_Color("#f6f6f6");
$img->text_color = new Securimage_Color("#333333");
$img->noise_color = new Securimage_Color("#999999");
$img->line_color = new Securimage_Color("#666666");

// Add some distortion
$img->num_lines = 5;
$img->noise_level = 0.5;

// Display the image
$img->show();