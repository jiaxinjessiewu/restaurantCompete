<?php
	ini_set('display_errors', 'On');
	require_once "lib/lib.php";
	require_once "model/Compete.php";
	require_once "model/Rating.php";
	session_save_path("sess");
	session_start(); 

	$dbconn = db_connect();
	$_SESSION['Connection'] = $dbconn;
	$errors=array();
	$view="";

	/* Insert all restaurants into work table*/
	$restaurants = file("dev/restaurants.txt");
	$file_line = count($restaurants);
	$count_query = "SELECT *  FROM work;";
	$count_result = pg_query($dbconn, $count_query);
	$line = pg_num_rows($count_result);	
	if($line == 0){
		foreach($restaurants as $read){

			$insert_query = "INSERT INTO work VALUES ('$read');";
			$result = pg_query($dbconn,$insert_query);
			if(!$result){
				echo 'An error occurred.';
				exit;
			}
		}
	}

	/* controller code */
	if(!isset($_SESSION['state'])){
		$_SESSION['state']='login';
	}

	if(isset($_GET['operation'])){
		$_SESSION['state'] = $_GET['operation'];
	}
	switch($_SESSION['state']){
		case "unavailable":
			$view="unavailable.php";
			break;

		case "login":
			// the view we display by default
			$view="login.php";

			// check if submit or not
			if(empty($_REQUEST['submit']) || $_REQUEST['submit']!="login"){
				break;
			}

			// validate and set errors
			if(empty($_REQUEST['user'])){
				$errors[]='user is required';
			}
			if(empty($_REQUEST['password'])){
				$errors[]='password is required';
			}
			if(!empty($errors))break;

			// perform operation, switching state and view if necessary
			if(!$dbconn) return;
			$query = "SELECT * FROM appuser WHERE id=$1 and password=$2;";
        		$result = pg_prepare($dbconn, "", $query);

        		$result = pg_execute($dbconn, "", array($_REQUEST['user'], $_REQUEST['password']));
		
          		if($row = pg_fetch_array($result, NULL, PGSQL_ASSOC)){
				
				$_SESSION['state']='compete';
				$_SESSION['Compete']=new Compete($_REQUEST['user']);
				
				$_SESSION["Compete"]->get_restaurants();
				
				$view="voting.php";
			} else {
				$errors[]="Invalid Login. <br/> To sign up if you are new member";
			}
			break;
		case "compete":
			/* The view to let user vote on pairs of restaurants*/
			$view = "voting.php";
			if(empty($_REQUEST['vote1']) && empty($_REQUEST['vote2']) && empty($_REQUEST['vote3']))
			{
				break;
			}else if(empty($_REQUEST['vote2']) && empty($_REQUEST['vote3'])){
				$whoWin = 0;
			}else if(empty($_REQUEST['vote1']) && empty($_REQUEST['vote3'])){
				$whoWin = 1;
			}else{
				$whoWin = 2;
			}
			if(!empty($errors))break;
			
			if($whoWin<2){

			$id = pg_escape_string(utf8_encode($_SESSION["Compete"]->user_id));
			$val1 = $_SESSION["Compete"]->history()[0];
			$val1 = str_replace("'", "''", $val1);
			$val2 = $_SESSION["Compete"]->history()[1];
		
			$val2 = str_replace("'", "''", $val2);
			$query = "INSERT INTO voterecord VALUES ('$id','$val1','$val2');";
			$result = pg_query($dbconn,$query);

			$work_query1 = "SELECT * FROM work WHERE name= '$val1';";
			
			$result2 = pg_query($dbconn, $work_query1);
			
			if($row = pg_fetch_row($result2)){
				$rating_A = $row[1];
			}

			$work_query2 = "SELECT * FROM work WHERE name='$val2';";
			$result3 = pg_query($dbconn, $work_query2);
			if($row = pg_fetch_row($result3)){
				$rating_B = $row[1];
			}

			if($whoWin)$rating = $_SESSION["Compete"]->make_vote($rating_A, $rating_B, Rating::LOST, Rating::WIN);
			else $rating = $_SESSION["Compete"]->make_vote($rating_A, $rating_B, Rating::WIN, Rating::LOST);


			$query = "UPDATE work SET rate=$1 WHERE name=$2;";
			$result = pg_prepare($dbconn, "my_query4", $query);
			$result = pg_execute($dbconn, "my_query4", array($rating['a'],$_SESSION["Compete"]->history()[0]));
			$query = "UPDATE work SET rate=$1 WHERE name=$2;";
			$result = pg_prepare($dbconn, "my_query5", $query);
			$result = pg_execute($dbconn, "my_query5", array($rating['b'],$_SESSION["Compete"]->history()[1]));
			}
			$_REQUEST['vote1']="";$_REQUEST['vote2']="";$whoWin = "";
			$_SESSION["Compete"]->get_restaurants();
			break;
		case "results":
				/* The view to show the rating result in descending order*/
            	$view="results.php";
				break;
		case "logout":
			session_save_path("sess");
			session_unset();
			$_SESSION['state']="login";
			$view = "login.php";
			session_destroy();
			break;
		case "signup":
			/* The view when the new user create new accout*/
			$view="signup.php";
			if(empty($_REQUEST['submit']) || $_REQUEST['submit']!="Creat Account"){
				break;
			}
			if(empty($_REQUEST['user'])){
				$errors[]='user is required';
			}
			if(empty($_REQUEST['password'])){
				$errors[]='password is required';
			}
			if(!empty($errors))break;
			if(!$dbconn) { echo("Can't connect to the database");exit;}
			$userid = $_POST['user'];
			$check_query = "SELECT * FROM appuser WHERE id='".$userid."';";
			$result = pg_query($dbconn,$check_query);
			if(!pg_fetch_assoc($result)){
				$insert_query = "INSERT INTO appuser VALUES ($1,$2);";
				$rr = pg_prepare($dbconn, "", $insert_query);
				$rr = pg_execute($dbconn, "", array($_REQUEST['user'], $_REQUEST['password']));
				$_SESSION['state']='compete';
				$_SESSION['Compete']=new Compete($_REQUEST['user']);
				$_SESSION['Compete']->get_restaurants();
				$view="voting.php";

			}else{
				$errors[]="user already exists";
			}
			break;	
		}
	require_once "view/$view";
?>

