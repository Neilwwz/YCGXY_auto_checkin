<?php
include("header.php");
//If user is already login, exit this page
if (isset($_SESSION["isLogin"]) and $_SESSION["isLogin"]) {
    echo "<script>alert('你已登录!');window.location.href='userindex.php';</script>";
    exit;
}


function login($username, $password)
{
    global $conn;
    if (userexist($username)) {
        if (password_verify($password, mysqli_fetch_assoc(mysqli_query($conn, "SELECT password FROM users WHERE username='{$username}';"))["password"])) {
            return array(true, "登陆成功");
        } else {
            return array(false, "密码错误");
        }
    } else {
        return array(false, "用户不存在");
    }
}

function startlogin($username, $password)
{
    $result = login($username, $password);
    if ($result[0]) {
        $_SESSION['isLogin'] = true;
        $_SESSION['user_id'] = get_id_by_username($username);
        echo "<script>window.location.href='userindex.php';</script>";
    } else {
        echo "<script>alert('$result[1]');window.location.href='index.php#login';</script>";
    }
}

//Click the login bottom
if (isset($_POST['login'])) {
    if ($_POST["username"] == null or $_POST["password"] == null) {
        echo "<script>alert('用户名或密码不能为空!');window.location.href='index.php#login';</script>";
        exit;
    } else {
        startlogin($_POST["username"], $_POST["password"]);
    }
} elseif (isset($_POST['register'])) {
    if ($_POST["username"] == null or $_POST["password"] == null) {
        echo "<script>alert('用户名或密码不能为空!');window.location.href='index.php#login';</script>";
        exit;
    } else {
        $feed = register($_POST["username"], $_POST["password"]);
        if (!$feed[0]) {
            echo "<script>alert('$feed[1]');window.location.href='index.php#login';</script>";
        } else {
            echo "<script>alert('注册成功!');window.location.href='index.php#login';</script>";
        }

    }
}
?>
<html lang="zh-CN">
<head>
    <title>Login</title>
</head>
<body>
<div class="container"
     style="align-self: center; position: relative;width: <?php echo((isMobile()) ? "auto" : "30%"); ?>;margin-top: 5%">
    <h1>这是废弃的登录页面，请<a href="index.php#login" style="text-decoration: none">点击这里</a>回到正常登陆</h1>
    <!--    <div class="card border-dark">-->
    <!--        <h4 class="card-header bg-primary text-white text-center">登录/注册</h4>-->
    <!--        <div class="card-body" style="margin:0 5% 5% 5%;">-->
    <!--            <form action="login.php" method="post">-->
    <!--                <div class="input-group mb-3">-->
    <!--                    <span class="input-group-text" id="username_input"><i class="bi bi-envelope-fill"></i>用户名</span>-->
    <!--                    <input type="username" name="username" class="form-control" aria-describedby="username_input">-->
    <!--                </div>-->
    <!--                <br>-->
    <!--                <div class="input-group mb-3">-->
    <!--                    <span class="input-group-text" id="password_input"><i class="bi bi-shield-lock-fill"></i>密码</span>-->
    <!--                    <input type="password" name="password" class="form-control" aria-describedby="password_input">-->
    <!--                </div>-->
    <!--                <button name="login" class="btn btn-primary" type="submit">登录</button>-->
    <!--                <button name="register" class="btn btn-success" type="submit">注册</button>-->
    <!--            </form>-->
    <!--            <a href='index.php' class='btn btn-secondary' style="margin-top: 10%;">返回首页</a>-->
    <!--        </div>-->
    <!--    </div>-->
</div>
</body>