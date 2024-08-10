<?php include("Common.php"); ?>
<?php Session::verify($configuration); ?>

<html>

<head><?php PageWriter::elementHeadWrite("Projects Pledged To"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<form name="formProjectsPledgedTo" action="" method="post">
		<div>
			<div>
				<label><b>Organizer:</b></label>

				<?php
					$session = $_SESSION["Session"];
					$userID = $_Get["UserID"];
					if ($userID == null)
					{
						$userLoggedIn = $session->user;
						$userID = $userLoggedIn->userID;
					}
					$persistenceClient = $_SESSION["PersistenceClient"];
					$user = $persistenceClient->userGetByID($userID);
					$userName = $user->nameFull;
					echo $userName
				?>
			</div>
			<br />

			<label><b>Projects Proposed:</b></label><br />

			<?php
				$projectsProposed = $persistenceClient->projectsGetByUserIDOrganizer($userID);

				$projectsProposedCount = count($projectsProposed);
				if ($projectsProposedCount == 0)
				{
					echo "(no projects proposed)";
				}
				else
				{
					echo "<table style='border:1px solid' width='100%'>";
					echo "<thead>";
					echo "<th>Name</th>";
					echo "<th>Date Proposed</th>";
					echo "<th>Status</th>";
					echo "<th>Pledged</th>";
					echo "<th>Goal</th>";
					echo "<th>Actions</th>";
					echo "</thead>";
					foreach ($projectsProposed as $project)
					{
						$projectID = $project->projectID;
						$projectName = $project->name;
						$status = $project->isActive ? "Active" : "Inactive";
						$timeProposed = strtotime( $project->timeProposed );
						$timeProposedFormatted = date("Y/m/d", $timeProposed);
						$pledgeAmountTotalInUsd = $persistenceClient->userProjectPledgesSumGetByProjectID($projectID);

						$projectAsListItem =
							"<tr>"
							. "<td>" . $projectName . "</td>"
							. "<td>" . $timeProposedFormatted . "</td>"
							. "<td>" . $status . "</td>"
							. "<td>$" . $pledgeAmountTotalInUsd . "</td>"
							. "<td>$" . $project->goalInUsd . "</td>"
							. "<td><a href='ProjectDetails.php?projectID=$projectID'>Details</a></td>"
							. "</tr>";
						echo($projectAsListItem);
					}
					echo "</table>";
				}
			?>
		</div>
		<a href="ProjectSearch.php">Browse Available Projects</a><br />
		<a href='UserDetails.php'>Back to User Details</a><br />

	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
