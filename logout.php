<?php
require_once 'config.php';

// Fazer logout
session_unset();
session_destroy();

// Redireciona para Login
header('Location: login.php');
exit;
