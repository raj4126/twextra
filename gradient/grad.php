<?php

header("Content-type: image/png");

//GET TYPE (THAT USER WANTS)
$type = @$_GET['type'];
if ($type == false) {
    $type = 'top';
}

//GET DIMENTIONS (THAT USER WANTS)
$height = intval(@$_GET['height']);
if ($height == 0) {
    $height = 50;
}
$width = intval(@$_GET['width']);
if ($width == 0) {
    $width = 100;
}

//GET HEX COLOURS (THAT USER WANTS)
$start_colour = $_GET['start_colour'];
if ($start_colour == false) {
    $start_colour = '000000';
}
$end_colour = $_GET['end_colour'];
if ($end_colour == false) {
    $end_colour = 'FFFFFF';
}

//CONVERT HEX COLOURS TO RGB
$hex_r  = substr($start_colour, 0, 2);
$hex_g = substr($start_colour, 2, 2);
$hex_b = substr($start_colour, 4, 2);

$start_r = hexdec($hex_r);
$start_g = hexdec($hex_g);
$start_b = hexdec($hex_b);

$hex_r  = substr($end_colour, 0, 2);
$hex_g = substr($end_colour, 2, 2);
$hex_b = substr($end_colour, 4, 2);

$end_r = hexdec($hex_r);
$end_g = hexdec($hex_g);
$end_b = hexdec($hex_b);

//CREATE BLANK IMAGE
$image = @imagecreate($width, $height) or die("Cannot Initialize new GD image stream");

if ($type == 'top') {
    
    //LOOP THROUGH ALL THE PIXELS
    for($y = 0; $y < $height; $y++) {
        
        //LOOP THROUGH ROW
        for($x=0; $x < $width; $x++) {
            
            //CALCULATE THIS ROWS RGB COLOURS
            
            if ($start_r == $end_r) {
                $new_r = $start_r;
            }
            $difference = $start_r - $end_r;
            $new_r = $start_r - intval(($difference / $height) * $y);
            
            //====
            
            if ($start_g == $end_g) {
                $new_g = $start_g;
            }
            $difference = $start_g - $end_g;
            $new_g = $start_g - intval(($difference / $height) * $y);
            
            //===
            
            if ($start_b == $end_b) {
                $new_b = $start_b;
            }
            $difference = $start_b - $end_b;
            $new_b = $start_b - intval(($difference / $height) * $y);
            
            //===
            
            //ALLOCATE THE COLOR
            $row_color = imagecolorresolve($image, $new_r, $new_g, $new_b);
            
            //CREATE ROW OF THIS COLOR
            imagesetpixel($image, $x, $y, $row_color);
            
        }
        
    }

}

if ($type == 'left') {
    
    //LOOP THROUGH ALL THE PIXELS
    for($x = 0; $x < $width; $x++) {
        
        //LOOP THROUGH COLUMN
        for($y=0; $y < $height; $y++) {
            
            //CALCULATE THIS ROWS RGB COLOURS
            
            if ($start_r == $end_r) {
                $new_r = $start_r;
            }
            $difference = $start_r - $end_r;
            $new_r = $start_r - intval(($difference / $width) * $x);
            
            //====
            
            if ($start_g == $end_g) {
                $new_g = $start_g;
            }
            $difference = $start_g - $end_g;
            $new_g = $start_g - intval(($difference / $width) * $x);
            
            //===
            
            if ($start_b == $end_b) {
                $new_b = $start_b;
            }
            $difference = $start_b - $end_b;
            $new_b = $start_b - intval(($difference / $width) * $x);
            
            //===
            
            //ALLOCATE THE COLOR
            $row_color = imagecolorresolve($image, $new_r, $new_g, $new_b);
            
            //CREATE ROW OF THIS COLOR
            imagesetpixel($image, $x, $y, $row_color);
            
        }
        
    }
    
}

imagepng($image);
imagedestroy($image);

?>