<?php
/**
 * Securimage CAPTCHA Class.
 */
class Securimage {
    public $image_width = 215;
    public $image_height = 80;
    public $font_ratio = 0.4;
    public $code_length = 6;
    public $image_bg_color = '#ffffff';
    public $text_color = '#707070';
    public $line_color = '#707070';
    public $noise_color = '#707070';
    public $ttf_file;
    public $perturbation = 0.75;
    public $num_lines = 6;
    public $noise_level = 5;
    public $charset = 'ABCDEFGHKLMNPRSTUVWYZ23456789';
    public $no_spaces = true;
    public $code;
    public $im;

    public function __construct() {
        $this->ttf_file = dirname(__FILE__) . '/AHGBold.ttf';
        $this->code = $this->generateCode();
    }

    public function show($background_image = '') {
        if (function_exists('session_start') && session_id() == '') {
            session_start();
        }

        $this->createImage();
        $_SESSION['securimage_code_value'] = strtolower($this->code);
        header('Content-Type: image/png');
        imagepng($this->im);
        imagedestroy($this->im);
        exit();
    }

    public function check($code) {
        if (function_exists('session_start') && session_id() == '') {
            session_start();
        }

        $code = strtolower($code);
        $stored = isset($_SESSION['securimage_code_value']) ? $_SESSION['securimage_code_value'] : '';
        unset($_SESSION['securimage_code_value']);
        return $code == $stored;
    }

    protected function generateCode() {
        $chars = str_split($this->charset);
        $code = '';
        for ($i = 0; $i < $this->code_length; ++$i) {
            $code .= $chars[array_rand($chars)];
        }
        return $code;
    }

    protected function createImage() {
        $this->im = imagecreatetruecolor($this->image_width, $this->image_height);
        $bg_color = $this->hexToRGB($this->image_bg_color);
        $bg = imagecolorallocate($this->im, $bg_color[0], $bg_color[1], $bg_color[2]);
        imagefill($this->im, 0, 0, $bg);

        // Add noise
        for ($i = 0; $i < $this->noise_level; ++$i) {
            $noise_color = $this->hexToRGB($this->noise_color);
            $noise = imagecolorallocate($this->im, $noise_color[0], $noise_color[1], $noise_color[2]);
            for ($j = 0; $j < 5; ++$j) {
                imagesetpixel($this->im, rand(0, $this->image_width), rand(0, $this->image_height), $noise);
            }
        }

        // Add lines
        for ($i = 0; $i < $this->num_lines; ++$i) {
            $line_color = $this->hexToRGB($this->line_color);
            $color = imagecolorallocate($this->im, $line_color[0], $line_color[1], $line_color[2]);
            imageline($this->im, rand(0, $this->image_width), rand(0, $this->image_height), 
                     rand(0, $this->image_width), rand(0, $this->image_height), $color);
        }

        // Add text
        $text_color = $this->hexToRGB($this->text_color);
        $color = imagecolorallocate($this->im, $text_color[0], $text_color[1], $text_color[2]);
        $font_size = $this->image_height * $this->font_ratio;
        $x = ($this->image_width - strlen($this->code) * $font_size) / 2;
        $y = $this->image_height - ($this->image_height - $font_size) / 2;

        for ($i = 0; $i < strlen($this->code); ++$i) {
            $angle = rand(-15, 15);
            imagettftext($this->im, $font_size, $angle, $x + $i * $font_size, $y, $color, $this->ttf_file, $this->code[$i]);
        }
    }

    protected function hexToRGB($hex) {
        $hex = ltrim($hex, '#');
        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        return array_map('hexdec', str_split($hex, 2));
    }
}