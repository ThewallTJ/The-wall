<?php

// CHECKEN OF DE GEBRUIKER HIER WEL HOORT TE ZIJN
if (!isset($_POST['submit_login'])) {
    header("Location: login.php");
}

// CHECKEN OF DE GEBRUIKER ALLES HEEFT INGEVULD
if (empty($_POST['username']) OR empty($_POST['password'])) {
    echo 'je bent iets vergeten in te vullen.<br>';
    echo 'Klik <a href="login.php">hier</a> om het nog eens te proberen.';
    exit();
}

// CHECKEN OF DE GEBRUIKER BESTAAT ( EN OF ZIJN WACHTWOORD KLOPT)
require ('../../private/db.php');
$query = "SELECT userid, hash, active FROM users WHERE username = ? AND password = ?";
$stmt = $mysqli->prepare($query) or die ('error preparing.');
$stmt->bind_param( 'ss',$username, $password) or die ('Error binding params.');
$stmt->bind_result( $userid, $hash, $active) or die ('Error binding results');
$username = $_POST['username'];
$password = $_POST['password'];
$password = hash('sha512', $password) or die ('Error hashing');
$stmt->execute() or die ('Error executing.');
$fetch_success = $stmt->fetch();

echo 'Variabelen:<br> ' . $userid . '<br> ' . $hash . '<br> ' . $active . '<br>';

if (!$fetch_success) {
    echo 'Sorry, er is iets misgegaan.<br>';
    echo 'Klik <a href="login.php">hier</a> om het nog eens te proberen.';
    exit();

} else if ($active == 0) {
    echo 'Sorry, je account is nog niets geactiveerd. Check je mailbox.';
    echo 'Klik <a href="login.php">hier</a> om het nog eens te proberen.';
    exit();
}

setcookie( 'userid', $userid, time() + 3600 * 24 * 7);
setcookie( 'hash', $hash, time() + 3600 * 24 * 7);
setcookie( 'username', $username, time() + 3600 * 24 * 7);
header( 'Location: homepage.php');