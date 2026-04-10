<?php
session_start();
session_destroy();

// arahkan ke login (karena login ada di views)
header("Location: views/login.php");
exit;