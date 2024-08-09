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
				$userLoggedIn = $session->user;
				$pledges = $userLoggedIn->pledges;
				$persistenceClient = $_SESSION["PersistenceClient"];

				$numberOfPledges = count($pledges);
				if ($numberOfPledges == 0)
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

						$pledgeID = $pledge->pledgeID;
						$pledgeAsListItem = "<li>" . "todo - Pledge ID " . $pledgeID . "</li>";
						echo($pledgeAsListItem);
					}
					echo "</ul>";
				}
			?>
		</div>
		<div>
			<label><b>Pledge Transfers Incoming from Other Users:</b></label><br />
			<div>
				<?php
					$transfersIncoming = $persistenceClient->pledgesGetByTransferTarget($userLoggedIn->username, $userLoggedIn->emailAddress);
					$numberOfTransfersIncoming = count($transfersIncoming);
					if ($numberOfTransfersIncoming == 0)
					{
						echo "(no items)";
					}
					else
					{
						echo "<ul>";
						foreach ($transfersIncoming as $transfer)
						{
							$userID = $transfer->userID;
							$userTransferring = $persistenceClient->userGetByID($userID);
							$projectID = $transfer->projectID;
							$project = $projectsAll[$projectID];
							$projectName = $project->name;
							$pledgeID = $transfer->pledgeID;
							$claimLink = "<a href='PledgeTransferClaim.php?pledgeID=" . $pledgeID. "'>Claim</a>";
							$transferAsString = "'" . $projectName . "' from " . $userTransferring->username . " - " . $claimLink;
							$transferAsListItem = "<li>" . $transferAsString . "</li>";
							echo($transferAsListItem);
						}
						echo "</ul>";
					}
				?>
			</div>
			<a href='PledgeTransferClaimByCode.php'>Claim a Pledge by Transfer Code</a>

		</div>
		<br />
		<a href="ProjectSearch.php">Browse Available Projects</a><br />
		<a href='User.php'>Back to Account Details</a><br />

	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
