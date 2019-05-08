<?php

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css" />
		<title>RestaurantMash</title>
	</head>
	<body>
		<header><h1>RestaurantMash</h1></header>
		<nav>
			<ul>
			<li> <a class="current" ref="?operation=compete">Compete</a>
                        <li> <a href="?operation=results">Results</a>
                        <li> <a href="?operation=logout">Logout</a>
                        </ul>
		</nav>
		<main>
			<h1>Compete</h1>
			<h2>Which restaurant would you rather go to?</h2>
			
			<form action="index.php" method="post">
				<table>
					<tr>
						<th class="choice"><input type="submit" name="vote1" value="<?php $value = $_SESSION["Compete"]->history();$val1 = $value[0];echo $val1; ?>"></th>
						<th>or</th>
						<th class="choice"><input type="submit" name="vote2" value="<?php $value = $_SESSION["Compete"]->history();$val2 = $value[1];echo $val2; ?>"></th>
						<th>or</th>
						<th class="choice"><input type="submit" name="vote3" value="<?php $value =0 ; echo "I don't know"; ?>"></th>
					</tr>
				</table> 
			</form> 
		</main>
		<footer>
		</footer>
	</body>
</html>

