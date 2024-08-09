<?php include("Common.php"); ?>
<?php Session::verify($configuration); ?>

<html>

<head><?php PageWriter::elementHeadWrite("Account Details"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>User Details:</b></label><br /><br />
		<label>Name:</label>
		<label>
		<?php
			$session = $_SESSION["Session"];
			$userID = $_GET["userID"];
			if ($userID == null)
			{
				$userLoggedIn = $session->user;
				$userID = $userLoggedIn->userID;
			}
			$persistenceClient = $_SESSION["PersistenceClient"];
			$user = $persistenceClient->userGetByID($userID);
			echo($user->nameFull);
			if ($userLoggedIn->userID == $userID)
			{
				echo(" (" . $user->username . ")");
			}
		?>
		</label><br /><br />

		<label>Projects Proposed:</label>
		<?php
			$projectsCount = $persistenceClient->projectsCountGetByUserIDOrganizer($userID);
			echo("" . $projectsCount);
			echo("<a href='UserProposals.php'>Details</a>");
		?>
		<br /><br />
		

		<label>Pledges Active:</label>
		<?php
			$persistenceClient = $_SESSION["PersistenceClient"];
			$pledgesCount = $persistenceClient->userProjectPledgesCountGetByUserID($userID);
			echo("" . $pledgesCount);
			echo("<a href='UserPledges.php'>Details</a>");
		?>
		<br /><br />

		<a href="ProjectSearch.php">Browse Available Projects</a><br />

		<?php
			if ($userLoggedIn->userID == $userID)
			{
				echo("<br />\n");
				echo("<a href='UserLogout.php'>Log Out</a><br />\n");
				echo("<a href='UserDelete.php'>Delete Account</a><br />\n");
			}
		?>
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
