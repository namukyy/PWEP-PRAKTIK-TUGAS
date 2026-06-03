<?php
session_start();
if (isset($_SESSION['login'])) {
    header('Location: pages/beranda.php');
} else {
    header('Location: pages/login.php');
}
exit;
