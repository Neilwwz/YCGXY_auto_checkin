<?php
include("header.php");
$currentuser = new User($_SESSION['user_id']);
$currenttask = new Task($currentuser->task_id);
if (isset($_POST['submit'])) {
    if (($_POST['username'] == "") or (($_POST['password']) == "")) {
        alert("请填写用户名和密码");
    } else {
        $currenttask->update($_POST['username'], $_POST['password'], $_POST['wxpusher_uid'], $_SESSION['user_id']);
        alert("任务更新成功");
        echo "<script>window.location.href='userindex.php';</script>";
        exit;
    }
} else if (isset($_POST['delete'])) {
    $currenttask->delete();
    alert("任务删除成功");
    echo "<script>window.location.href='userindex.php#checkin';</script>";
    exit;
}
?>
<div class="container" style="margin-top: 2%;width: <?php echo (isMobile()) ? "auto" : "50%"; ?>;">
    <div class='card border-dark'>
        <h4 class='card-header bg-primary text-white text-center'>编辑任务</h4>
        <form action='' method='post' style="margin: 20px;">
            <div class="input-group mb-3">
                <span class='input-group-text' id='name'>UoM Username</span>
                <input type='text' required class='form-control' name='username' autocomplete='off'
                       value='<?php echo $currenttask->username; ?>'>
            </div>
            <div class="input-group mb-3">
                <span class='input-group-text' id='name'>Password</span>
                <input type='password' required class='form-control' name='password' autocomplete='off'
                       value='<?php echo $currenttask->password; ?>'>
            </div>
            <div class="input-group mb-3">
                <span class='input-group-text' id='name'>WxPusher UID</span>
                <input type='text' class='form-control' name='wxpusher_uid' autocomplete='off' placeholder='不需要请留空'
                       value='<?php echo $currenttask->wxpusher_uid; ?>'>
            </div>
            <input type='submit' class='btn btn-primary' name='submit' value='保存'>
            <input type='submit' class='btn btn-danger' name='delete' value='清空'>
        </form>
    </div>
</div>
