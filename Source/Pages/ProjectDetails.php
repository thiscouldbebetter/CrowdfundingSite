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
				$session = $_SESSION["Session"];
				$userLoggedIn = $session->user;
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

					$status = $project->isActive ? "Active" : "<b>INACTIVE</b>";
					echo("<label>Status: " . $status . "</label><br /><br />");

					$pledgesTotalInUsd = $persistenceClient->userProjectPledgesSumGetByProjectID($projectID);
					echo("<label>Total Pledged by All Users So Far: $" . $pledgesTotalInUsd . "</label><br/><br />");

					echo("<label>Goal: $" . $project->goalInUsd . "</label><br/><br />");
					if ($project->isActive)
					{
						$pledgesExisting = $persistenceClient->userProjectPledgesGetByUserIDAndProjectID(
							$userLoggedIn->userID, $projectID
						);
						$pledgesExistingCount = count($pledgesExisting);
						if ($pledgesExistingCount > 1)
						{
							echo("ERROR: More that one active pledge already exists.");
						}
						else
						{
			?>

				<form action="PledgeConfirm.php?projectID=<?php echo($projectID); ?>" method="post">
					<label>
						<?php
							echo(
								$pledgesExistingCount == 0
								? "Amount to Pledge"
								: "Amount You Have Pledged"
							);
						?>
						(USD): $
					</label>

					<input
						name="AmountToPledgeInUsd"
						type="number"
						min="1"
						max="100"
						step="1"
						readonly="<?php echo($pledgesExistingCount == 0 ? "false" : "true") ?>"
						value="
							<?php
								echo
								(
									$pledgesExistingCount == 0
									? 1
									: 1
								);
							?>
						"
					>
					</input>
					<button type="submit">Pledge</button>
				</form>
			<?php
						}
					}
					else
					{
						echo("<label>This project is no longer active, and so is not accepting pledges.</label><br /><br />");
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
