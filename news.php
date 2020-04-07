<?php
    include_once "functions.php";
    include_once "system/system.php";

    $id = intval($mysqli->real_escape_string($_GET['id']));

    if(!isset($id) || $id <= 0)
    {
        addAdminLog($user['id'], "Был использовани ID: ".$_GET['id']." для новости.");
        redirMsg("Ошибка", "Данного идентификатора новости не существует.", "/");
        exit;
    }
    if(isset($_POST['submit']) && userCheck(0) == true)
    {
        $comment = htmlspecialchars($mysqli->real_escape_string($_POST['comment']));
        $date = date("d.m.Y H:i:s");
        $mysqli->query("INSERT INTO `comments` (`text`, `author`, `date`, `sourse`, `post`) VALUES ('".$comment."', '".$user['id']."', '".$date."', 'news', '".$id."')");
        
        redirMsg("Уведомление", "Вы успешно прокомментировани новость.", "/news/".$id."/");
    }
    if(isset($_GET['delete']) > 0 && userCheck(0) == true)
    {
        $_delete = $mysqli->real_escape_string($_GET['delete']);
        $query = $mysqli->query("SELECT `text`, `author` FROM `comments` WHERE `post` = '".$id."' AND `id` = '".$_delete."'");
        $com = $query->fetch_assoc();

        addAdminLog($user['id'], "Удалил комментарий: <i>".$com['text']."</i>. Пользователя: ".smallUserData($com['author'], 2));

        $mysqli->query("UPDATE `comments` SET `text` = '<font color=red>Комментарий был удалён администратором</font>' WHERE `post` = '".$id."' AND `id` = '".$_delete."'");

        redirMsg("Уведомление", "Вы успешно удалили комментарий.", "/news/".$id."/");
    }

    $checkViewsQuery = $mysqli->query("SELECT * FROM `ipvisitors` WHERE `ip` = '".$_SERVER['REMOTE_ADDR']."' AND `ref` = 'news' AND `post` = '".$id."'");
    if($checkViewsQuery->num_rows == 0)
    {
        $mysqli->query("INSERT INTO `ipvisitors` (`ip`, `post`, `ref`, `date`) VALUES ('".$_SERVER['REMOTE_ADDR']."', '".$id."', 'news', '".$date."')");
        $mysqli->query("UPDATE `news` SET `views` = `views` + 1 WHERE `id` = '".$id."'");
    }

    $queryComments = $mysqli->query("SELECT * FROM `comments` WHERE `sourse` = 'news' AND `post` = '".$id."' ORDER BY `id` DESC");

    $query = $mysqli->query("SELECT * FROM `news` WHERE `id` = '".$id."' LIMIT 1");
    $news = $query->fetch_assoc();

    $title = $news['title'];
    $keywords = $news['keywords'];
    $desc = mb_strimwidth($news['text'], 0, 200);

    include_once "system/header.php";
?>

<div class="col-sm mainMenu">
<span style='font-size: 14px'><?=smallUserData($news['author'])?></span>
	<span style="float:right;font-size: 14px"><?=$news['date']?></span>
<center>
	<b><?=$news['title']?></b>
	<br>
	<br>
	<br>
<?php
if(!empty($news['img']))
    echo '<img src="/images/news/'.$news['img'].'" style="box-shadow: 0px 0px 5px black;" width="250px" height="360px"/>';
?>
</center>
<br>
<?=$news['text']?>
<br>
<br>
<div style="float: right">
<?
    if(userCheck(0))
    {
?>
<a href=""><div class="button">Редактировать</div></a>
<a href=""><div class="button">Удалить</div></a>
<?
    }
?>
<a href=""><div class="button">Пожаловаться</div></a>
</div>
</div>
<br>
<div class="col-sm mainMenu" style="font-size: 14px">Теги: <?=$news['keywords']?></div>
<br>
<div class="col-sm mainMenu" style="font-size: 14px">
		Комментарии: <?=$queryComments->num_rows;?> <span style="float: right">Просмотры: <?=$news['views']?></span>

</div>
<br>
<form method="POST">
    <div class="mainMenu">
        <textarea name="comment" id="comment" cols="155px" rows="5" style="resize: none;background: #37516c;color: white;"></textarea><br>
        <input name="submit" type="submit" value="Добавить комментарий" />
    </div>
</form>
<br>
<?php
while($comments = $queryComments->fetch_assoc())
{
?>
<div class="mainMenu">
<?=smallUserData($comments['author'])?>: <?=$comments['text']?>
<?
    if(userCheck(0))
        echo '<span style="float:right;color: #f14242;"><a href="/news/'.$id.'/'.$comments['id'].'/">[Удалить]</a></span>';
?>
<br>
<span style="float:right"><?=$comments['date']?></span>
<br>
</div>
<?
}
?>
<br>
<br>
<br>
<br>
<br>
<br>