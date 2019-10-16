<html>
<head>
<meta charset="utf-8">
</head>
<body>
<form action="mission_5.php"method="post">
<?php
//データベースへの接続
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//テーブル作成
$sql="CREATE TABLE IF NOT EXISTS keijiban"
."("
."id INT AUTO_INCREMENT PRIMARY KEY,"
."name varchar(32),"
."comment TEXT,"
."pass varchar(32),"
."nitiji DATETIME"
.");";
$stmt=$pdo->query($sql);

//編集か新規投稿かでフォーム分ける
//(新規)
if(empty($_POST["hensyu"])){
		echo"名前:<input type='text' name='name' >
		コメント:<input type='text' name='comment' >
		password:<input type='text' name='pass'>
		<input type='submit' value='送信'><br>";
		
		//新規投稿
	if(isset($_POST["comment"],$_POST["name"])){
		$sql=$pdo->prepare("INSERT INTO keijiban (name, comment,pass,nitiji) VALUES (:name, :comment, :pass, cast(now() as datetime))");
		$sql->bindParam(':name',$name,PDO::PARAM_STR);
		$sql->bindParam(':comment',$comment,PDO::PARAM_STR);
		$sql->bindParam(':pass',$pass,PDO::PARAM_STR);
		$name=$_POST["name"];
		$comment=$_POST["comment"];
		$pass=$_POST["pass"];
		//クエリの実行
		$sql->execute();
	}

}
else{
//(編集)
		$id=$_POST["hensyu"];
		$sql='SELECT * FROM keijiban where id=:id';
		$stmt=$pdo->prepare($sql);
		$stmt->bindParam(':id',$id,PDO::PARAM_INT);
		$stmt->execute();		
		$result=$stmt->fetch();
		echo"名前:<input type='text' name='name1' value='".$result['name']."'>
		コメント:<input type='text' name='comment1' value='".$result['comment']."'>
		password:<input type='text' name='pass1'>
		<input type='hidden' name='kakusu' value='".$result['id']."'>
		<input type='submit' value='送信'><br>";
}

?>
<br>削除番号:<input type="text" value="" name="delete">
	password:<input type="text" name="pass2">
<input type="submit" value="削除"></br>
<br>編集番号:<input type="text" name="hensyu">
<input type="submit" value="編集"></br>
</form>

<?php
//番号一致で編集
//（編集番号(hidden)の受け取り）
if(isset($_POST["comment1"],$_POST["name1"],$_POST["kakusu"])){
	$sql='SELECT * FROM keijiban where id=:id';
		$stmt=$pdo->prepare($sql);
		$stmt->bindParam(':id',$id,PDO::PARAM_INT);
		$stmt->execute();		
		$result=$stmt->fetch();	
		$id=$_POST["kakusu"];
		$name=$_POST["name1"];
		$comment=$_POST["comment1"];
//（pass一致で編集）
		$pass=$_POST["pass1"];
		$sql='update keijiban set name=:name,comment=:comment where id=:id and pass=:pass';
			$stmt=$pdo->prepare($sql);
			$stmt->bindParam(':name',$name,PDO::PARAM_STR);
			$stmt->bindParam(':comment',$comment,PDO::PARAM_STR);
			$stmt->bindParam(':id',$id,PDO::PARAM_INT);
			$stmt->bindParam(':pass',$pass,PDO::PARAM_STR);
			$stmt->execute();
}

//番号とpass一致で削除
//（削除番号の受け取り）
elseif(!empty($_POST["delete"])){
		$id=$_POST["delete"];
		$sql='SELECT * FROM keijiban where id=:id';
			$stmt=$pdo->prepare($sql);
			$stmt->bindParam(':id',$id,PDO::PARAM_INT);
			$stmt->execute();		
			$result=$stmt->fetch();	
//（pass一致で削除）
			$pass=$_POST["pass2"];
			$sql='delete from keijiban where id=:id and pass=:pass';
				$stmt=$pdo->prepare($sql);
				$stmt->bindParam(':id',$id,PDO::PARAM_INT);
				$stmt->bindParam(':pass',$pass,PDO::PARAM_STR);
				$stmt->execute();
	
}



//select（データを取得して表示する）
$sql='SELECT * FROM keijiban';
$stmt=$pdo->query($sql);
$results=$stmt->fetchAll();
foreach($results as $row){
	echo $row['id'].',';
	echo$row['name'].',';
	echo$row['comment'].',';
	echo$row['nitiji'].'<br>';
echo"<hr>";
}

?>
</body>
</html>