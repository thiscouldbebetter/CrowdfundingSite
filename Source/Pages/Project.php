<?php include("Common.php"); ?>

<html>

<head><?php PageWriter::elementHeadWrite("Project Details"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>Project Proposal Details:</b></label><br />
		<br />
		<div>
			<?php
				$persistenceClient = $_SESSION["PersistenceClient"];
				$projectID = $_GET["projectID"];
				$project = $persistenceClient->projectGetByID($projectID);
				if ($project == null)
				{
					echo("No project with the specified ID could be found.");
				}
				else
				{
					$projectName = $project->name;
					echo("<label>Name: " . $projectName . "</label><br /><br />");

					$userOrganizer = $persistenceClient->userGetByID($project->userIDOrganizer);
					$userOrganizerName = $userOrganizer->nameFull;
					echo("<label>Organizer: " . $userOrganizerName . "</label><br /><br />");

					$timeProposed = strtotime($project->timeProposed);
					$timeProposedFormatted = date("Y/m/d", $timeProposed);
					echo("<label>Date Proposed: " . $timeProposedFormatted . "</label><br /><br />");
	
					$pledgesTotalInUsd = $persistenceClient->userProjectPledgesSumGetByProjectID($projectID);
					echo("<label>Total Pledged So Far: $" . $pledgesTotalInUsd . "</label><br/><br />");
	
					echo("<label>Goal: $" . $project->goalInUsd . "</label><br/><br />");
					if ($project->isActive)
					{
			?>
			
				<form action="PledgeConfirm.php?projectID=<?php echo($projectID); ?>" method="post">
					<label>Amount to Pledge (USD): $</label>
					<input name="AmountToPledgeInUsd" type="number" min="1" max="100" step="1" value="1"></input>
					<button type="submit">Pledge</button>
				</form>
				
			<?php
					}
					else
					{
						echo("<label>This project is no longer active, and so is not accepting pledges.</label>");
					}

					echo "<label>Description:</label>";
					echo "<p>" . $project->description . "</p>";
				}
			?>

			<br />

		</div>
		<br />

		<a href="ProjectSearch.php">Browse Other Projects</a>
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
