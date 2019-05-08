<?php
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="refresh" content="30" />
		<link rel="stylesheet" type="text/css" href="style.css" />
		<title>RestaurantMash</title>
	</head>
	<body>
		<header><h1>RestaurantMash</h1></header>
		<nav>
			<ul>
			<li> <a href="?operation=compete">Compete</a>
                        <li> <a class="current" href="?operation=results">Results</a>
                        <li> <a href="?operation=logout">Logout</a>
                        </ul>
		</nav>
		<main>
			<h1>Results</h1>
			<form method="post">
				<table>
					<tr> <th>Restaurant</th><th>Rating</th> </tr>
					<?php
					$query = "SELECT * FROM work ORDER BY rate DESC;";
					$result = pg_query($_SESSION['Connection'],$query);

					$all = pg_fetch_all($result);
					foreach($all as $array){
						echo '<tr><td>'.$array['name'].'</td><td>'.$array['rate'].'</td></tr>';
					}
					?>
				</table>
			</form>
			
		</main>
		<footer>
		</footer>
	</body>
</html>

