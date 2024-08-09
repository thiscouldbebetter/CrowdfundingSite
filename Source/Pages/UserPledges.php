<?php include("Common.php"); ?>
<?php Session::verify($configuration); ?>

<html>

<head><?php PageWriter::elementHeadWrite("Pledges Made"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<form name="formPledgesOwned" action="" method="post">
		<div>
			<label><b>Pledges Made:</b></label><br />

			<?php
				$session = $_SESSION["Session"];
				$userID = $_GET["UserID"];
				if ($userID == null)
				{
					$userLoggedIn = $session->user;
					$userID = $userLoggedIn->userID;
				}
				$persistenceClient = $_SESSION["PersistenceClient"];
				$pledges = $persistenceClient->userProjectPledgesGetByUserID($userID);

				$pledgesCount = count($pledges);
				if ($pledgesCount == 0)
				{
					echo "(no pledges)";
				}
				else
				{
					echo "<ul>";
					foreach ($pledges as $pledge)
					{
						$projectID = $pledge->projectID;
						$project = $projectsAll[$projectID];
						$projectName = $project->name;

						$project = $persistenceClient->projectGetByID($pledge->projectID);
						$projectUserOrganizer = $persistenceClient->userGetByID($project->userIDOrganizer);
						$projectAsString =
							"\"" . $project->name
							. "\", by " . $projectUserOrganizer->nameFull
							. ", $" . $pledge->pledgeAmountInUsd;
						$pledgeAsListItem = "<li>" . $projectAsString . "</li>";
						echo($pledgeAsListItem);
					}
					echo "</ul>";
				}
			?>
		</div>
		<a href="ProjectSearch.php">Browse Available Projects</a><br />
		<a href='UserDetails.php'>Back to User Details</a><br />

	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
