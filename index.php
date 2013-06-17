<?php require_once("includes/connection.php");?>
<?php require_once("includes/functions.php");?>
<?php
  if(isset($_POST['submit']))
	{
		$count = send_messages();
		$message = send_confirmation_email($count);
	}
?>

<html>
	<head>
		<title>Auto-Textor</title>
	</head>
	<body>
		<table>
			<td>
				<form action="index.php" method="post">
					Messages Sent:<?php echo "{$count}";?><br>
					Confirmation Email?:<?php echo "{$message}";?><br><br>
					<input type="submit" name="submit" value="Send Messages" /><br><br>
					<a href="reports.php?sent=1">Reports-Sent Info</a><br>
					<a href="reports.php?sent=9">Reports-Failed Info</a><br>
					<a href="reports.php">Reports-Unsent Info</a>
				</form>
			</td>
		</table>
	</body>
</html>

<?php require_once("includes/footer.php");?>
