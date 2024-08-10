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

				$pledgeAmountInUsd = $_POST["AmountToPledgeInUsd"];

				if (isset($_POST["IsConfirmed"]) == false || $_POST["IsConfirmed"] == false)
				{
					echo("<label>Organizer: " . $userOrganizer->nameFull . "</label><br /><br />");
				}
				else
				{
					$timePledged = date("Y-m-d H:i:s");

					$pledge = new UserProjectPledge(
						null, // ID
						$userLoggedIn->userID,
						$projectID,
						$pledgeAmountInUsd,
						$timePledged,
						true // $isActive
					);

					$persistenceClient->userProjectPledgeSave($pledge);

					header("Location: ProjectDetails.php?projectID=" . $projectID);
				}

		?>

				<form action="" method="post">
					<label>Amount to Pledge: $</label>
					<input name="AmountToPledgeInUsd" type="number" readonly="true" value="<?php echo($pledgeAmountInUsd) ?>"></input>
					<br /><br />
					<label>Activate this checkbox and then click the confirm button to confirm this pledge:</label>
					<input name="IsConfirmed" type="checkbox"></input>
					<button type="submit">Confirm</button>
					<a href="ProjectDetails.php?projectID=<?php echo($projectID) ?>">Cancel</a>
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