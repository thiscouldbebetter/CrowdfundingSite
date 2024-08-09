<?php include("Common.php"); ?>
<?php Session::verify($configuration); ?>

<html>

<head><?php PageWriter::elementHeadWrite("Projects Pledged To"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<form name="formProjectsPledgedTo" action="" method="post">
		<div>
			<label><b>Projects Pledged to:</b></label><br />

			<?php
				$session = $_SESSION["Session"];
				$userLoggedIn = $session->user;
				$pledges = $userLoggedIn->pledges;
				$persistenceClient = $_SESSION["PersistenceClient"];

				$pledgeCount = count($pledges);
				if ($pledgeCount == 0)
				{
					echo "(no pledges)";
				}
				else
				{
					echo "<ul>";
					foreach ($pledges as $pledge)
					{
						$projectId = $pledge->projectID;
						$project = $persistenceClient->projectGetById($projectId);
						$projecName = $project->name;

						$pledgeAsListItem = "<li>" . $pledgeName . "</li>";
						echo($pledgeAsListItem);
					}
					echo "</ul>";
				}
			?>
		</div>
		<a href="ProjectSearch.php">Browse Available Projects</a><br />
		<a href='User.php'>Back to Account Details</a><br />

	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
