<?php
var_dump(getenv('GOOGLE_CLIENT_ID'));
var_dump($_SERVER['GOOGLE_CLIENT_ID'] ?? null);
var_dump($_ENV['GOOGLE_CLIENT_ID'] ?? null);
?>