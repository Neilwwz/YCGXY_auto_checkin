<?php
include("include/common.php");
if (!isset($_SESSION['isLogin'])) {
    $_SESSION['isLogin'] = false;
}
if ((!$_SESSION['isLogin']) and (php_self() != "login.php" and php_self() != "index.php")) {
    echo "<script>window.location.href='index.php#login';</script>"; // Redirect to login page
    exit;
}
?>