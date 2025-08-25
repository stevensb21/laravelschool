<?php
echo "PHP Configuration File: " . php_ini_loaded_file() . "<br>";
echo "Additional .ini files: " . implode(", ", php_ini_scanned_files() ?: []) . "<br>";
echo "Current PHP version: " . phpversion() . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
?>
