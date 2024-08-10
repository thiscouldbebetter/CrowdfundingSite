<?php include("Common.php"); ?>

<html>

<head><?php PageWriter::elementHeadWrite("Project Search"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<?php
		$persistenceClient = $_SESSION["PersistenceClient"];
		$session = $_SESSION["Session"];
		if ($session != null)
		{
			$userLoggedIn = $session->user;
		}
		else
		{
			$userLoggedIn = User::dummy();
		}

		$projectNamePartial = (isset($_POST["ProjectNamePartial"]) ? $_POST["ProjectNamePartial"] : "");
		$projectsPerPage = (isset($_POST["ProjectsPerPage"]) ? $_POST["ProjectsPerPage"] : 10);
		$pageNumber = (isset($_POST["PageNumber"]) ? $_POST["PageNumber"] : 1);
		$pageIndex = $pageNumber - 1;
		$numberOfProjectsFound =
			$persistenceClient->projectsSearchCount($projectNamePartial);
		$projectsFound =
			$persistenceClient->projectsSearch($projectNamePartial, $projectsPerPage, $pageIndex);
		$numberOfPages = ceil($numberOfProjectsFound / $projectsPerPage);
	?>

	<div class="divCentered">
		<label><b>Project Search:</b></label><br />
		<br />
		<div>
			<div>
				<label>Search Criteria:</label><br />
				<form action="" method="post">
					<label>Project Name:</label>
					<input name="ProjectNamePartial" value="<?php echo $projectNamePartial; ?>"></input>
					<button type="submit">Search</button>
				</form>
			</div>
		<div>
			<label>Search Results:</label><br />
			<br />
			<div>
				<?php
					echo($numberOfProjectsFound . " projects found.<br /><br />");
				?>

				<form action="" method="post">

					<label>Results per Page:</label>
					<select
						name="ProjectsPerPage"
						value="<?php echo $projectsPerPage; ?>"
						onchange="this.form.submit();"
					>
						<?php
							$pageSizes = [ 10, 20, 50 ];
							foreach ($pageSizes as $pageSizeAvailable)
							{
								$isPageSizeSelected = ($pageSizeAvailable == $projectsPerPage);
								$pageSizeAsOption =
									"<option value='" . $pageSizeAvailable . "' "
									. ($isPageSizeSelected ? "selected='true'" : "")
									. ">"
									. $pageSizeAvailable
									. "</option>";
								echo $pageSizeAsOption;
							}
						?>
					</select>

					<label>Page Number:</label>
					<input
						name="PageNumber"
						type="number"
						style="width:4em"
						value="<?php echo $pageNumber; ?>"
						onchange="this.form.submit();"
					>
					</input>
					<label> of </label>
					<input
						type="number"
						style="width:4em"
						disabled="true"
						value="<?php echo $numberOfPages; ?>"
					>
					</input>
				</form>

				<table style="border:1px solid" width="100%">
					<thead>
						<th>Name</th>
						<th>Organizer</th>
						<th>Date Proposed</th>
						<th>Pledged (USD)</th>
						<th>Goal (USD)</th>
					</thead>
					<?php
						foreach ($projectsFound as $project)
						{
							$tableRow = "<tr>";

							$projectID = $project->projectID;

							$projectName = $project->name;
							$tableCell = "<td><a href='ProjectDetails.php?projectID=" . $projectID . "'>" . $projectName . "</a></td>";
							$tableRow = $tableRow . $tableCell;

							$userOrganizer = $persistenceClient->userGetByID($project->userIDOrganizer);
							$tableCell = "<td><a href='UserDetails.php?userID=" . $userOrganizer->userID . "'>" . $userOrganizer->nameFull . "</a></td>";
							$tableRow = $tableRow . $tableCell;

							$timeProposed = strtotime($project->timeProposed);
							$timeProposedFormatted = date("Y/m/d", $timeProposed);
							$tableCell = "<td>" . $timeProposedFormatted . "</td>";
							$tableRow = $tableRow . $tableCell;

							$pledgesInUsd = $persistenceClient->userProjectPledgesSumGetByProjectID($projectID);
							$tableCell = "<td>$". $pledgesInUsd . "</td>";
							$tableRow = $tableRow . $tableCell;

							$tableCell = "<td>$". $project->goalInUsd . "</td>";
							$tableRow = $tableRow . $tableCell;

							$tableRow = $tableRow . "</tr>";
							echo($tableRow);
						}
					?>
				</table>
			</div>
		</div>
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
