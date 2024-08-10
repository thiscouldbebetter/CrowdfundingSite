<?php include("Common.php"); ?>

<html>

<head><?php PageWriter::elementHeadWrite("Project Details"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>Create a New Project Proposal:</b></label><br />
		<br />
		<div>
			<?php
				$session = $_SESSION["Session"];
				$userLoggedIn = $session->user;

				$projectFieldsAreSpecified =
					isset($_POST["ProjectName"])
					&& isset($_POST["ProjectGoalInUsd"])
					&& isset($_POST["ProjectDescription"]);

				if ($projectFieldsAreSpecified)
				{
					$projectName = $_POST["ProjectName"];
					$projectGoalInUsd = $_POST["ProjectGoalInUsd"];
					$projectDescription = $_POST["ProjectDescription"];
					$timeProposed = new DateTime();

					$project = new Project(
						null, // $projectID,
						$userLoggedIn->userID,
						$projectName,
						$projectGoalInUsd,
						$timeProposed,
						true, // $isActive,
						$projectDescription
					);

					$persistenceClient = $_SESSION["PersistenceClient"];
					$persistenceClient->projectSave($project);
					$projectID = $project->projectID;
					header("Location: ProjectDetails.php?projectID=$projectID");
				}
				else
				{
			?>

					<label>Organizer: <?php echo($userLoggedIn->nameFull); ?></label>

					<form action="" method="post">

						<div>
							<label>Project Name:</label>
							<input name="ProjectName"></input>
						</div>

						<div>
							<label>Pledged Funding Goal (USD): $</label>
							<input
								name="ProjectGoalInUsd"
								type="number"
								min="1000"
								max="1000000"
								step="1"
							/>
						</div>

						<div>
							<label>Description:</label>
							<br />
							<textarea
								name="ProjectDescription"
								cols="40"
								rows="10"
							></textarea>
						</div>

						<button type="submit">Create</button>
					</form>
			<?php
				}
			?>

		</div>

		<a href='UserDetails.php'>Back to User Details</a><br />
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
