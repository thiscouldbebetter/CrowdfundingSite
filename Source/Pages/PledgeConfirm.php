<?php include("Common.php"); ?>

<html>

<head><?php PageWriter::elementHeadWrite("Pledge Confirmation"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>Pledge Confirmation:</b></label><br />
		<br />

		<?php
			$persistenceClient = $_SESSION["PersistenceClient"];
			$session = $_SESSION["Session"];
			if ($session != null)
			{
				$userLoggedIn = $session->user;
				$projectID = $_GET["projectID"];
				$project = $persistenceClient->projectGetByID($projectID);
				echo("<label>Project: " . $project->name . "</label><br /><br />");

				$userOrganizer = $persistenceClient->userGetById($project->userIDOrganizer);
				echo("<label>Organizer: " . $userOrganizer->nameFull . "</label><br /><br />");

				$pledgeAmountInUsd = $_POST["AmountToPledgeInUsd"];
				echo("<label>Amount to Pledge: $" . $pledgeAmountInUsd . "</label><br /><br />");
		?>
		
				<form action="PledgeSave.php" method="post">
					<button type="submit">Confirm</button>
				</form>
		
		<?php
			}
			else
			{
				echo("Not logged in!");
				header("Location: UserLogin.php");
			}
		?>

	</div>

	<?php PageWriter::footerWrite(); ?>
	
</body>

</html>