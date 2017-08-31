<?php
include './functions.php';
include './Controller.php';
include './WeChatController.php';

$wechatObj = new WeChatController();

$fun = isset($_GET['fun']) ? $_GET['fun'] : 'index';

$wechatObj->$fun();

?>

