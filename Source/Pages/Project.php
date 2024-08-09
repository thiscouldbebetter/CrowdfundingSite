<?php include("Common.php"); ?>

<html>

<head><?php PageWriter::elementHeadWrite("Project Details"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>Project Details:</b></label><br />
		<br />
		<div style="text-align:center">
			<?php
				$persistenceClient = $_SESSION["PersistenceClient"];
				$projectID = $_GET["projectID"];
				$project = $persistenceClient->projectGetByID($projectID);
				if ($project == null)
				{
					echo "No project with the specified ID could be found.";
				}
				else
				{
					$projectName = $project->name;
					echo "<label>" . $projectName . "</label><br /><br />";
					echo "<label>Goal: $" . $project->goalInUsd . "</label><br/><br />";
					if ($project->isActive)
					{
						echo
							"<a href='UserProjectPledgeCreate.php?projectID=" . $projectID . ">"
							. "Pledge"
							. "</a>";
					}
					else
					{
						echo "<label>This project is no longer accepting pledges.</label>";
					}
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
