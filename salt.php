<?php
// Salt Generator
// Created By Dharaninja
// Email : dhara.s3curity@gmail.com

function generateRandomString($length = 64) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

echo "<center><h1>STRONG SALT GENERATOR</h1></center>";
echo "<center><h3>Created By a.k.a Dharaninja</h3></center><br>";
echo "<center>SALT : <strong>".generateRandomString()."</strong></center><br>";
?>
