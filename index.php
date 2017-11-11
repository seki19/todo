<?php

require_once("function.php");
$errors = array();

if(isset($_POST['submit'])){
    
    $name = $_POST['name'];
    $memo = $_POST['memo'];

    $name = htmlspecialchars($name, ENT_QUOTES);
    $memo = htmlspecialchars($memo, ENT_QUOTES);

    if($name === ''){
        $errors['name'] = 'お名前が入力されていません。';
    }

    if($memo === ''){
        $errors['memo'] = 'メモが入力されていません。';
    }
    
    if(count($errors) === 0){
        
    	$dbh = db_connect();

        $sql = 'INSERT INTO task (name, memo, done) VALUES (?, ?, 0)';
        $stmt = $dbh->prepare($sql);

        
        $stmt->bindValue(1, $name, PDO::PARAM_STR);
        $stmt->bindValue(2, $memo, PDO::PARAM_STR);

        $stmt->execute();

        $dbh = null;

        unset($name, $memo);
    }
}

if(isset($_POST['method']) && ($_POST['method'] === 'put')){
    
    $id = $_POST["id"];
    $id = htmlspecialchars($id, ENT_QUOTES);
    $id = (int)$id;

    $dbh = db_connect();

    $sql = 'UPDATE task SET done = 1  WHERE id = ?';
    $stmt = $dbh->prepare($sql);
    
    
    $stmt->bindValue(1, $id, PDO::PARAM_INT);
    $stmt->execute();

    $dbh = null;

}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>likes</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<h1>likes</h1>
<?php
if(isset($errors)){
    print("<ul>");
    foreach($errors as $value){
        print("<li>");
        print($value);
        print("</li>");
    }
    print("</ul>");
}
?>
<form action="index.php" method="post" id="form">
<ul>
    <li><span>作品タイトル</span><input type="text" name="name" value="<?php if (isset($name)){ print($name); } ?>"</li>

    <li><span>メモ</span><textarea name="memo"><?php if(isset($memo)) { print($memo); } ?></textarea></li>

    <li><span>カテゴリ</span><select name="example1">
    	<option value="<?php if (isset($name)){ print($name); } ?>">音楽</option>
		<option value="<?php if (isset($name)){ print($name); } ?>">漫画</option>
		<option value="<?php if (isset($name)){ print($name); } ?>">アニメ</option>
		<option value="<?php if (isset($name)){ print($name); } ?>">スポーツ</option>
		<option value="<?php if (isset($name)){ print($name); } ?>">ファッション</option>
		</select>
    	</li>

    <li><span>いつからハマった</span><input type="date" name="name" value="<?php if (isset($created)){ print($created); } ?>"</li>

    <li><input type="submit" name="submit"></li>
</ul>
</form>
<?php
	
	$dbh = db_connect();

	$sql = 'SELECT id, name, memo FROM task WHERE done = 0 ORDER BY id DESC';
	$stmt = $dbh->prepare($sql);
	$stmt->execute();
	$dbh = null;

	print('<dl>');
	print('<div>');
	while($task = $stmt->fetch(PDO::FETCH_ASSOC)){
    	print('<div id="list">');
	    //if ($task['done'] === '0') {
		    print '<dt>';
		    print $task["name"];
		    print '</dt>';

		    print '<dd>';
		    print $task["memo"];
		    print '</dd>';

		    print '<dd>';
		    print '
		            <form action="index.php" method="post">
		            <input type="hidden" name="method" value="put">
		            <input type="hidden" name="id" value="' . $task['id'] . '">
		            <button type="submit">済んだ</button>
		            </form>
		          ' ;
		    print '</dd>';

		    print '<hr>';
		print('</div>');
	//}
}
	print('</div)');
	print('</dl>');
?>
</body>
</html>