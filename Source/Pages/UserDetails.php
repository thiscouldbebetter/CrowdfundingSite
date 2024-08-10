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
			$projectsCount =
				$persistenceClient->projectsCountGetByUserIDOrganizer($userID);
			echo("" . $projectsCount);
			echo("<a href='UserProposals.php'>Details</a>");
			if ($userLoggedIn->userID == $userID)
			{
		?>

				<a href="ProjectCreate.php">Propose New Project</a>

		<?php
			}
		?>
		<br /><br />
		

		<label>Pledges Active:</label>
		<?php
			$persistenceClient = $_SESSION["PersistenceClient"];
			$pledgesCount =
				$persistenceClient->userProjectPledgesCountGetByUserID($userID);
			echo("" . $pledgesCount);
			echo("<a href='UserPledges.php'>Details</a>");
		?>
		<br /><br />

		<a href="ProjectSearch.php">Browse Available Projects</a><br />

		<?php
			if ($userLoggedIn->userID == $userID)
			{
		?>
			<br />
			<a href='UserLogout.php'>Log Out</a><br />
			<a href='UserDelete.php'>Delete Account</a><br />
		<?php
			}
		?>
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
