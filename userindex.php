<?php
include("header.php");
if (isset($_GET['logout'])) {
    logout();
    exit();
}
$currentuser = new user($_SESSION['user_id']);
$currenttask = new task($currentuser->task_id);
?>
<head>
    <meta charset="utf-8"/>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"/>
    <link rel="stylesheet" href="/resources/css/main.css"/>
    <noscript>
        <link rel="stylesheet" href="/resources/css/noscript.css"/>
    </noscript>
    <link href="https://jsd.cloudsides.com/npm/nprogress@0.2.0/nprogress.min.css" rel="stylesheet"/>
    <script src="https://jsd.cloudsides.com/npm/nprogress@0.2.0/nprogress.min.js"></script>
    <title>个人中心 - 盐城工学院自动上报</title>
</head>
<body>
<div id="wrapper">
    <header id="header">
        <div class="logo">
            <span class="icon fa-user"></span>
        </div>
        <div class="content">
            <div class="inner">
                <h1>个人中心</h1>
                <p>User Center</p>
                <div>
                    <h3>公告：<b><?php echo get_notice() ?></b></h3>
                </div>
                <div>
                    <h3><b><?php echo get_user_task_id($_SESSION['user_id']) == -1 ? "您未设置签到任务" : "您已设置签到任务" ?></b></h3>
                </div>
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="#checkin">管理签到</a></li>
                <li><a href="#info">个人信息</a></li>
                <?php if ((isset($_SESSION['user_id'])) and (isadmin($_SESSION['user_id'])))
                    echo "<li><a href='/admin'>管理面板</a></li>" ?>
                <li><a href="userindex.php?logout">退出登录</a></li>
            </ul>
        </nav>
    </header>
    <div id="main">
        <article id="checkin">
            <h2 class="major">管理签到</h2>
            <form action="checkin_manage.php" method="post">
                <div class="field">
                    <label for="username">身份证号码</label>
                    <input type='text' required name='username' autocomplete='off'
                           value='<?php echo $currenttask->username; ?>'/>
                </div>
                <div class="field">
                    <label for="password">修改后的密码</label>
                    <input type='password' required name='password' autocomplete='off'
                           value='<?php echo $currenttask->password; ?>'/>
                </div>
                <div class="field">
                    <label for="wxpusher_uid">WxPusher UID</label>
                    <input type='text' name='wxpusher_uid' autocomplete='off' placeholder='不需要请留空'
                           value='<?php echo $currenttask->wxpusher_uid; ?>'>
                </div>
                <ul class="actions">
                    <li><input type="submit" value="保存" class="primary special" name="submit"/></li>
                    <li><input type="submit" value="删除我的任务" class="primary" name="delete"/></li>
                    <li><a href="//wxpusher.zjiecode.com/api/qrcode/qPyymTWBd9Qe0aA3Gjy3cE5wA5m7SAvB3Jx2rZaNZuLXcez1VJ7saWqlZddbVzAl.jpg" target="_blank"><input type="button" value="关注微信通知中心" class="primary"></a></li>
                </ul>
            </form>
        </article>
        <article id="info">
            <h2 class="major">个人信息</h2>
            <p>您的用户名是：<b><?php echo $currentuser->username; ?></p>
            <form action="user_info.php" method="post">
                <div class="field">
                    <label for="password">密码</label>
                    <input type='password' class='form-control' name='password' placeholder='不修改请留空'>
                </div>
                <ul class="actions">
                    <li><input type="submit" value="保存" class="primary special" name="submit"/></li>
                </ul>
            </form>
        </article>
    </div>
    <?php include 'footer.php'; ?>
</div>
<div id="bg"></div>
<script src="https://jsd.cloudsides.com/npm/jquery@1.11.3"></script>
<script src="https://jsd.cloudsides.com/gh/ajlkn/skel@3.0.1/dist/skel.min.js"></script>
<script src="/resources/js/util.js"></script>
<script src="/resources/js/main.js"></script>
<script>
    $(function () {
        $(window).load(function () {
            NProgress.done();
        });
        NProgress.set(0.0);
        NProgress.configure({showSpinner: false});
        NProgress.configure({minimum: 0.4});
        NProgress.configure({easing: 'ease', speed: 1200});
        NProgress.configure({trickleSpeed: 200});
        NProgress.configure({trickleRate: 0.2, trickleSpeed: 1200});
        NProgress.inc();
        $(window).ready(function () {
            NProgress.start();
        });
    });
</script>
</body>
