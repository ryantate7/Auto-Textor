<?php require_once("includes/connection.php");?>
<?php require_once("includes/functions.php");?>
<?php
  $current_date = get_current_date();
	
	if($_GET['sent'] == null)
	{
		$subject = "Unsent";
		$data_set = get_data();
		$record_count = mysql_num_rows($data_set);
	}
	
	elseif($_GET['sent'] == 1)
	{
		$subject = "Sent";
		$data_set = get_data($sent = 1);
		$record_count = mysql_num_rows($data_set);
	}
	
	else
	{
		$subject = "Failed";
		$data_set = get_data($sent = 9);
		$record_count = mysql_num_rows($data_set);
	}

	
	?>

<html>
	<head>
		<title>Reports</title>
	</head>
	<body>
		<h1>Reports for <?php echo $subject;?> Records</h1>
			<?php
			echo "<p>There are currently " . $record_count . " " . $subject . " records.<p>";
			echo "<h2><u>Records:</u></h2>";
			?>
			<table border="1">
			<?php			 
				echo "<tr><th>Name:</th> <th>Email Address:</th>";
				if($sent)
				{
					echo "<th>Date:</th><th>Status:</th>";
				}
				echo "</tr>";
				while($data = mysql_fetch_array($data_set))
				{
					echo "<tr><td>{$data['name']}</td> <td>{$data['phone']}&{$data['caddress']}</td>";
					if($sent)
					{
						echo "<td>{$data['date']}</td><td>{$data['sent']}</td>";
					}
					echo "</tr>";
				}			
				
			?>
			
		</table>
		<a href="index.php">Back to Main Page</a>
	</body>
</html>

<?php require_once("includes/footer.php");?>
