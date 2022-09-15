<?php
include("header.php");

if (isset($_POST['submit'])) {
    $currenttask = new task($_POST['task_id']);
    if (($currenttask->task_id) == -1) {
        alert("任务不存在");
        exit;
    }
    $currenttask->update($_POST['username'], $_POST['password'], $_POST['wxpusher_uid'], $_POST['userid']);
    echo '<div class="alert alert-success" role="alert"><p>保存成功</p></div>';
    echo '<script>window.setTimeout("window.location=\'tasks.php\'",800);</script>';
    exit;
}

if (isset($_GET['action'])) {
    if (!isset($_GET["id"])) {
        echo '<div class="alert alert-danger" role="alert"><p>参数错误</p></div>';
        exit;
    }
    $currenttask = new task($_GET["id"]);
    if ($currenttask->task_id == 0) {
        echo '<div class="alert alert-danger" role="alert"><p>任务不存在</p></div>';
        exit;
    }
    switch ($_GET["action"]) {
        case "edit":
        {
            break;
        }
        case "delete":
        {
            $currenttask->delete();
            echo '<div class="alert alert-success" role="alert"><p>任务删除成功</p></div>';
            echo '<script>window.setTimeout("window.location=\'tasks.php\'",800);</script>';
            exit;
        }
        default:
        {
            echo '<div class="alert alert-danger" role="alert"><p>action参数错误</p></div>';
            exit;
        }
    }
}

?>
<div class="container" style="margin-top: 2%;width: <?php echo (isMobile()) ? "auto" : "30%"; ?>;">
    <div class='card border-dark'>
        <h4 class='card-header bg-primary text-white text-center'>编辑任务</h4>
        <form action='' method='post' style="margin: 20px;">
            <div class="input-group mb-3">
                <span class='input-group-text' id='taskid'>任务ID</span>
                <input type='text' class='form-control' name='task_id' <?php echo "value='{$currenttask->task_id}'"; ?>
                       readonly>
            </div>
            <div class="input-group mb-3">
                <span class='input-group-text' id='name'>身份证号</span>
                <input type='text' required class='form-control' name='username' autocomplete='off'
                       value='<?php echo $currenttask->username; ?>'>
            </div>
            <div class="input-group mb-3">
                <span class='input-group-text' id='name'>密码</span>
                <input type='password' required class='form-control' name='password' autocomplete='off'
                       value='<?php echo $currenttask->password; ?>'>
            </div>
            <div class="input-group mb-3">
                <span class='input-group-text' id='name'>WxPusher UID</span>
                <input type='text' class='form-control' name='wxpusher_uid' placeholder='不需要请留空' autocomplete='off'
                       value='<?php echo $currenttask->wxpusher_uid; ?>'>
            </div>
            <div class="input-group mb-3">
                <span class='input-group-text' id='userid'>所属用户ID</span>
                <input type='text' class='form-control' name='userid' autocomplete='off'
                       value='<?php echo $currenttask->user_id; ?>' required>
            </div>
            <input type='submit' class='btn btn-primary' name='submit' value='保存'>
        </form>
    </div>
</div>