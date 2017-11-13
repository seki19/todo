<?php

//DB接続関数
require_once("function.php");
$errors = array();

if(isset($_POST['submit'])){
    
    //$_postに格納されている値を変数に代入する
    $name = $_POST['name'];
    $memo = $_POST['memo'];
    $created = $_POST['created'];
    $category_id = $_POST['category_id'];

    $name = htmlspecialchars($name, ENT_QUOTES);
    $memo = htmlspecialchars($memo, ENT_QUOTES);
    $created = htmlspecialchars($created, ENT_QUOTES);
    $category_id = htmlspecialchars((int)$category_id, ENT_QUOTES);

    if($name === ''){
        $errors['name'] = 'タイトルが入力されていません。';
    }

    if($memo === ''){
        $errors['memo'] = 'メモが入力されていません。';
    }

    if($created === ''){
        $errors['created'] = 'ハマった日付が入力されていません。';
    }
    
    if(count($errors) === 0){
        
    	$dbh = db_connect();

        $sql = 'INSERT INTO task (name, memo, done ,created , category_id) VALUES (?, ?, 0, ?, ?)';
        $stmt = $dbh->prepare($sql);

        
        $stmt->bindValue(1, $name, PDO::PARAM_STR);
        $stmt->bindValue(2, $memo, PDO::PARAM_STR);
        $stmt->bindValue(3, $created, PDO::PARAM_STR);
        $stmt->bindValue(4, $category_id, PDO::PARAM_STR);

        $stmt->execute();
        
        $dbh = null;

        unset($name, $memo, $created);
    }
}

if(isset($_POST['method']) && ($_POST['method'] === 'put')){
	//var_dump($_POST['finished']);
    if($_POST['finished'] !== ""){
	    $id = $_POST["id"];
	    $id = htmlspecialchars($id, ENT_QUOTES);
	    $id = (int)$id;

	    $finished = $_POST["finished"];
	    $finished = htmlspecialchars($finished, ENT_QUOTES);
	    

	    $dbh = db_connect();

	    $sql = "UPDATE task SET done = 1, finished = '$finished' WHERE id = ?";
	    $stmt = $dbh->prepare($sql);
	    
	    
	    $stmt->bindValue(1, $id, PDO::PARAM_INT);
	    $stmt->execute();
	    
	    $dbh = null;
	}else{
		$errors['finished'] = '飽きた日付が入力されていません。';
	}

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
<p>好きなもの、好きだったもの</p>
<hr>

<?php

if(isset($errors)){
    print("<ul>");
    foreach($errors as $value){
        print("<li>");
        print('!!');
        print($value);
        print('!!');
        print("</li>");
    }
    print("</ul>");
}
?>
<div div style=”border-style: solid ; border-width: 1px; padding: 10px 5px 10px 20px;”>
<form action="index.php" method="post" id="form">
<ul>
    <li>
    	<label for="name">タイトル</label>
    	<input type="text" name="name" value="<?php if (isset($name)){ print($name); } ?>"></li>

    <li>
    	<label for="memo">メモ</label>
    	<textarea name="memo"><?php if(isset($memo)) { print($memo); } ?></textarea></li>

    <li>
    	<label for="category">カテゴリ</label>
    	<select name="category_id">
    	<option value="0">音楽</option>
    	<option value="1">映画</option>
		<option value="2">書籍</option>
		<option value="3">アニメ、ゲーム</option>
		<option value="4">スポーツ</option>
		<option value="5">ファッション</option>
		</select>
    </li>

    <li><label for="created">いつからハマったか</label><input type="date" name="created" value="<?php if (isset($created)){ print($created); } ?>"</li>

    <li><input type="submit" name="submit"></li>
</ul>
</form>
</div>

<hr class="border">

<form method="post">
	<span>現在ハマっているもの:</span>
	<select  action="index.php" name="category_list">
			<option value="10">全件</option>
	    	<option value="0">音楽</option>
	    	<option value="1">映画</option>
			<option value="2">書籍</option>
			<option value="3">アニメ、ゲーム</option>
			<option value="4">スポーツ</option>
			<option value="5">ファッション</option>
	</select>
	<input type="submit" value="表示"/>
</form>
 <form method="post">	
 <span>年代別:</span>
	<select  action="index.php" name="age_list">
			<option value="10">全年代</option>
	    	<option value="0">小学校まで</option>
	    	<option value="1">中学校</option>
			<option value="2">高校</option>
			<option value="3">専門・大学</option>
			<option value="4">社会人</option>		
	</select>
 	<input type="submit" value="表示"/>
 </form>

<?php
	
	$dbh = db_connect();

	
	//カテゴリごとの表示
	if(isset($_POST['category_list'])){
		$category = (int)$_POST['category_list'];
		//var_dump($category);
		if($category === 10){
			$sql = 'SELECT id, name, memo, category_id, created, finished FROM task WHERE done = 0 ORDER BY id DESC';
		}else{
			$sql = "SELECT id, name, memo, category_id, created, finished FROM task WHERE done = 0 AND category_id = $category ORDER BY id DESC";
		//	var_dump($sql);
			
		}

	}else {
		$sql = 'SELECT id, name, memo, category_id, created, finished FROM task WHERE done = 0 ORDER BY id DESC';
	}

	//年代別の表示
	if(isset($_POST['age_list'])){
		
			$age_list = (int)$_POST['age_list'];
			//var_dump($age_list);
			switch ($age_list) {
				case '10':
					$sql = "SELECT id, name, memo, category_id, created, finished FROM task WHERE created >= '1996-03-06' AND created < '2020-12-30'  ORDER BY id DESC";
					break;
				case '0':
						$sql = "SELECT id, name, memo, category_id, created, finished FROM task WHERE created >= '1996-03-06' AND created < '2008-4-01'  ORDER BY id DESC";
					break;
				case '1':
						$sql = "SELECT id, name, memo, category_id, created, finished FROM task WHERE created >= '2008-4-01' AND created < '2011-4-01'  ORDER BY id DESC";
					break;
				case '2':
					$sql = "SELECT id, name, memo, category_id, created,finished FROM task WHERE created >= '2011-4-01' AND created < '2014-4-01'  ORDER BY id DESC";
					break;
				case '3':
					$sql = "SELECT id, name, memo, category_id, created,finished FROM task WHERE created >= '2014-4-01' AND created < '2016-4-01'  ORDER BY id DESC";
					break;
				case '4':
					$sql = "SELECT id, name, memo, category_id, created,finished FROM task WHERE created >= '2016-4-01' ORDER BY id DESC";
					break;
			}
			unset($_POST['age_list']);
		}
	
	$stmt = $dbh->prepare($sql);
	$stmt->execute();
	$dbh = null;
	print('<div id="list">');
	print('<p class="center">');
	print('<dl>');
	print('<div>');
	while($task = $stmt->fetch(PDO::FETCH_ASSOC)){
    	
	    //if ($task['done'] === '0') {
		    print '<dt>';
		    print $task["name"];
		    print '</dt>';

		    print '<dd>';
		    print $task["memo"];
		    print '</dd>';

		    print '<dd>';
		    print 'カテゴリー:';
		    switch ($task["category_id"]) {
		    	case '0':
		    		print "音楽";
		    		break;
		    	
		    	case '1':
		    		print "映画";
		    		break;

		    	case '2':
		    		print "書籍";
		    		break;

		    	case '3':
		    		print "アニメ、ゲーム";
		    		break;

		    	case '4':
		    		print "スポーツ";
		    		break;

		    	case '5':
		    		print "ファッション";
		    		break;
		    }
		    print '</dd>';

		    print '<dd>';
		    print 'ハマった日:';
		    print $task["created"];
		    print '</dd>';

		    print '<dd>';
		    if($task['finished'] === "0000-00-00"){
		    	print '
		            <form action="index.php" method="post">
		            <input type="hidden" name="method" value="put">
		            <input type="hidden" name="id" value="' . $task['id'] . '">
		            <input type="date" name="finished" value="<?php if (isset($finished)){ print($finished); } ?>">
		            <button type="submit">飽きた</button>
		            </form>
		          ' ;
		      }else{
		      	print '飽きた日:';
		      	print $task["finished"];
		      }
		    print '</dd>';

		    print '<hr>';
		print('</div>');
	//}
}
	print('</div)');
	print('</dl>');
	print('</p>');
?>
</body>
</html>