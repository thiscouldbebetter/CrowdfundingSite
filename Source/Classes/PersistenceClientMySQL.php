<?php

class PersistenceClientMySQL
{
	public function __construct($databaseServerName, $databaseUsername, $databasePassword, $databaseName)
	{
		$this->databaseServerName = $databaseServerName;
		$this->databaseUsername = $databaseUsername;
		$this->databasePassword = $databasePassword;
		$this->databaseName = $databaseName;
	}

	private function connect()
	{
		$databaseConnection = new mysqli($this->databaseServerName, $this->databaseUsername, $this->databasePassword, $this->databaseName);
		return $databaseConnection;
	}

	private function dateToString($date)
	{
		if ($date == null)
		{
			$returnValue = null;
		}
		else
		{
			$dateFormatString = "Y-m-d H:i:s";
			$returnValue = $date->format($dateFormatString);
		}

		return $returnValue;
	}

	public function notificationSave($notification)
	{
		$databaseConnection = $this->connect();

		if ($databaseConnection->connect_error)
		{
			die("Could not connect to database.");
		}

		if ($notification->notificationID == null)
		{
			$queryText =
				"insert into Notification (Addressee, Subject, Body, TimeCreated, TimeSent)"
				. " values (?, ?, ?, ?, ?)";
			$queryCommand = mysqli_prepare($databaseConnection, $queryText);
			$queryCommand->bind_param
			(
				"sssss",
				$notification->addressee, $notification->subject,
				$notification->body, $this->dateToString($notification->timeCreated),
				$this->dateToString($notification->timeSent)
			);
		}
		else
		{
			$queryText = "update Notification set Addressee = ?, Subject = ?, Body = ?, TimeCreated = ?, TimeSent = ? where NotificationID = ?";
			$queryCommand = mysqli_prepare($databaseConnection, $queryText);
			$queryCommand->bind_param
			(
				"sssssi",
				$notification->addressee, $notification->subject,
				$notification->body, $this->dateToString($notification->timeCreated),
				$this->dateToString($notification->timeSent),
				$notification->notificationID
			);
		}
		$didSaveSucceed = $queryCommand->execute();

		if ($didSaveSucceed == false)
		{
			die("Could not write to database.");
		}
		else
		{
			$notificationID = mysqli_insert_id($databaseConnection);
			if ($notificationID != null)
			{
				$notification->notificationID = $notificationID;
			}
		}

		$databaseConnection->close();

		return $notification;
	}

	public function projectGetByID($projectID)
	{
		$returnValue = null;

		$databaseConnection = $this->connect();

		$queryText = "select * from Project where ProjectID = ?";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $projectID);
		$queryCommand->execute();
		$queryCommand->bind_result(
			$projectID, $userIDOrganizer, $name, $goalInUsd, $timeProposed, $isActive, $description
		);

		while ($queryCommand->fetch())
		{
			$returnValue = new Project(
				$projectID, $userIDOrganizer, $name, $goalInUsd, $timeProposed, $isActive, $description
			);
			break;
		}

		$databaseConnection->close();

		return $returnValue;
	}

	public function projectSave($project)
	{
		$databaseConnection = $this->connect();

		if ($databaseConnection->connect_error)
		{
			die("Could not connect to database.");
		}

		if ($project->projectID == null)
		{
			$queryText =
				"insert into Project "
				. "(UserIDOrganizer, Name, GoalInUsd, TimeProposed, IsActive, Description)"
				. " values (?, ?, ?, ?, ?, ?)";
			$queryCommand = mysqli_prepare($databaseConnection, $queryText);
			$queryCommand->bind_param
			(
				"isisis",
				$project->userIDOrganizer,
				$project->name,
				$project->goalInUsd,
				$this->dateToString($project->timeProposed),
				$project->isActive,
				$project->description
			);
		}
		else
		{
			$queryText =
				"update Project set "
				. "UserIDOrganizer = ?, "
				. "Name = ?, "
				. "GoalInUsd = ?, "
				. "TimeProposed = ?, "
				. "IsActive = ?, "
				. "Description = ?, "
				. "where ProjectID = ?";
			$queryCommand = mysqli_prepare($databaseConnection, $queryText);
			$queryCommand->bind_param
			(
				"isisbi",
				$project->userIDOrganizer,
				$project->name,
				$project->goalInUsd,
				$this->dateToString($project->timeProposed),
				$project->isActive,
				$project->description,
				$project->projectID
			);
		}
		$didSaveSucceed = $queryCommand->execute();

		if ($didSaveSucceed == false)
		{
			die("Could not write to database.");
		}
		else
		{
			$projectID = mysqli_insert_id($databaseConnection);
			if ($projectID != null)
			{
				$project->projectID = $projectID;
			}
		}

		$databaseConnection->close();

		return $project;
	}

	public function projectsGetAll()
	{
		$returnValues = array();

		$databaseConnection = $this->connect();

		$queryText = "select * from Project";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->execute();
		$queryCommand->bind_result(
			$projectID, $userIDOrganizer, $name, $goalInUsd, $timeProposed, $isActive, $description
		);

		while ($queryCommand->fetch())
		{
			$project = new Project(
				$projectID, $userIDOrganizer, $name, $goalInUsd, $timeProposed, $isActive, $description
			);
			$returnValues[$projectID] = $project;
		}

		$databaseConnection->close();

		return $returnValues;
	}

	public function projectsGetByUserIDOrganizer($userIDOrganizer)
	{
		$returnValues = array();

		$databaseConnection = $this->connect();

		$queryText = "select * from Project where UserIDOrganizer = ?";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $userIDOrganizer);
		$queryCommand->execute();
		$queryCommand->bind_result(
			$projectID, $userIDOrganizer, $name, $goalInUsd, $timeProposed, $isActive, $description
		);

		while ($queryCommand->fetch())
		{
			$project = new Project(
				$projectID, $userIDOrganizer, $name, $goalInUsd, $timeProposed, $isActive, $description
			);
			$returnValues[] = $project;
		}

		$databaseConnection->close();

		return $returnValues;
	}

	public function projectsCountGetByUserIDOrganizer($userIDOrganizer)
	{
		$databaseConnection = $this->connect();

		$queryText = "select count('x') from Project where UserIDOrganizer = ?";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $userIDOrganizer);
		$queryCommand->execute();
		$queryCommand->bind_result($count);
		$queryCommand->fetch();

		$databaseConnection->close();

		return $count;
	}

	public function projectsSearch($projectNamePartial, $projectsPerPage, $pageIndex)
	{
		if ($projectsPerPage == null)
		{
			$projectsPerPage = 1000000;
		}
		if ($pageIndex == null)
		{
			$pageIndex = 0;
		}
		$pageOffsetInProjects = $pageIndex * $projectsPerPage;

		$returnValues = array();

		$databaseConnection = $this->connect();

		$projectNamePartial = "%" . $projectNamePartial . "%";

		$queryText =
			"select * from Project"
			. " where Name like ?"
			. " and IsActive = true"
			. " order by Name"
			. " limit ?, ?";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("sii", $projectNamePartial, $pageOffsetInProjects, $projectsPerPage);

		$queryCommand->execute();
		$queryCommand->bind_result(
			$projectID, $userIDOrganizer, $name, $goalInUsd, $timeProposed, $isActive, $description
		);

		while ($queryCommand->fetch())
		{
			$project = new Project(
				$projectID, $userIDOrganizer, $name, $goalInUsd, $timeProposed, $isActive, $description
			);
			$returnValues[$projectID] = $project;
		}

		$databaseConnection->close();

		return $returnValues;
	}

	public function projectsSearchCount($projectNamePartial)
	{
		$databaseConnection = $this->connect();

		$projectNamePartial = "%" . $projectNamePartial . "%";

		$queryText =
			"select count('x') from Project"
			. " where Name like ?"
			. " and IsActive = true";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("s", $projectNamePartial);

		$queryCommand->execute();
		$queryCommand->bind_result($projectCount);

		while ($queryCommand->fetch())
		{
			$returnValue = $projectCount;
		}

		$databaseConnection->close();

		return $returnValue;
	}

	public function sessionGetCurrentByUserID($userID)
	{
		$databaseConnection = $this->connect();

		$queryText = "select s.* from Session s where s.UserID = ? and s.TimeEnded is null and s.TimeStarted = (select max(s1.TimeStarted) from Session s1 where s1.TimeStarted <= ? and s1.UserID = s.UserID)";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$now = new DateTime();
		$nowAsString = $this->dateToString($now);
		$queryCommand->bind_param("is", $userID, $nowAsString);
		$queryCommand->execute();
		$queryCommand->bind_result($sessionID, $userID, $deviceAddress, $timeStarted, $timeUpdated, $timeEnded);

		$session = null;
		while ($queryCommand->fetch())
		{
			$user = User::dummy();
			$user->userID = $userID;
			$session = new Session($sessionID, $user, $deviceAddress, $timeStarted, $timeUpdated, $timeEnded);
			break;
		}

		$databaseConnection->close();

		return $session;
	}

	public function sessionSave($session)
	{
		$databaseConnection = $this->connect();

		if ($databaseConnection->connect_error)
		{
			die("Could not connect to database.");
		}

		$timeStartedAsString = $this->dateToString($session->timeStarted);
		$timeUpdatedAsString = $this->dateToString($session->timeUpdated);
		$timeEndedAsString = $this->dateToString($session->timeEnded);

		if ($session->sessionID == null)
		{
			$queryText =
				"insert into Session (UserID, DeviceAddress, TimeStarted, TimeUpdated, TimeEnded)"
				. " values (?, ?, ?, ?, ?)";
			$queryCommand = mysqli_prepare($databaseConnection, $queryText);
			$queryCommand->bind_param("issss", $session->user->userID, $session->deviceAddress, $timeStartedAsString, $timeUpdatedAsString, $timeEndedAsString);
		}
		else
		{
			$queryText =
				"update Session set UserID = ?, DeviceAddress = ?, TimeStarted = ?, TimeUpdated = ?, TimeEnded = ? where SessionID = ?";
			$queryCommand = mysqli_prepare($databaseConnection, $queryText);
			$queryCommand->bind_param("issssi", $session->user->userID, $session->deviceAddress, $timeStartedAsString, $timeUpdatedAsString, $timeEndedAsString, $session->sessionID);
		}

		$didSaveSucceed = $queryCommand->execute();

		if ($didSaveSucceed == false)
		{
			die("Could not write to database.");
		}
		else
		{
			$sessionID = mysqli_insert_id($databaseConnection);
			if ($sessionID != null)
			{
				$session->sessionID = $sessionID;
			}
		}

		$databaseConnection->close();

		return $session;
	}

	public function userDeleteByID($userID)
	{
		$databaseConnection = $this->connect();

		if ($databaseConnection->connect_error)
		{
			die("Could not connect to database.");
		}

		$queryText = "update User set IsActive = 0 where UserID = ?";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $userID);
		$didDeleteSucceed = $queryCommand->execute();

		return $didDeleteSucceed;
	}

	public function userGetByID($userID)
	{
		$databaseConnection = $this->connect();
		$queryText = "select * from User where UserID = ?";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $userID);
		$returnValue = $this->userGetByQueryCommand($queryCommand);
		$databaseConnection->close();
		return $returnValue;
	}

	public function userGetByEmailAddress($emailAddress)
	{
		$databaseConnection = $this->connect();
		$queryText = "select * from User where EmailAddress = ?";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("s", $emailAddress);
		$returnValue = $this->userGetByQueryCommand($queryCommand);
		$databaseConnection->close();
		return $returnValue;
	}

	public function userGetByUsername($username)
	{
		$databaseConnection = $this->connect();
		$queryText = "select * from User where Username = ? and IsActive = 1";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("s", $username);
		$returnValue = $this->userGetByQueryCommand($queryCommand);
		$databaseConnection->close();
		return $returnValue;
	}

	private function userGetByQueryCommand($queryCommand)
	{
		$queryCommand->execute();
		$queryCommand->bind_result($userID, $username, $emailAddress, $nameFull, $passwordSalt, $passwordHashed, $passwordResetCode, $isActive);
		$queryCommand->store_result();

		$numberOfRows = $queryCommand->num_rows();
		if ($numberOfRows == 0)
		{
			$userFound = null;
		}
		else
		{
			$queryCommand->fetch();

			$userFound = new User
			(
				$userID, $username, $emailAddress, $nameFull, $passwordSalt,
				$passwordHashed, $passwordResetCode, $isActive
			);
		}

		return $userFound;
	}

	public function userSave($user)
	{
		$databaseConnection = $this->connect();

		if ($databaseConnection->connect_error)
		{
			die("Could not connect to database.");
		}

		if ($user->userID == null)
		{
			$queryText =
				"insert into User (Username, EmailAddress, PasswordSalt, PasswordHashed, PasswordResetCode, IsActive)"
				. " values (?, ?, ?, ?, ?, ?)";
		}
		else
		{
			$queryText = "update User set username = ?, emailAddress = ?, passwordSalt = ?, passwordHashed = ?, passwordResetCode = ?, isActive=?";
		}

		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("sssssi", $user->username, $user->emailAddress, $user->passwordSalt, $user->passwordHashed, $user->passwordResetCode, $user->isActive);
		$didSaveSucceed = $queryCommand->execute();
		if ($didSaveSucceed == false)
		{
			die("Could not write to database.");
		}
		else
		{
			$userID = mysqli_insert_id($databaseConnection);
			if ($userID != null)
			{
				$user->userID = $userID;
			}
		}

		$databaseConnection->close();

		return $user;
	}

	public function userProjectPledgeGetByID($userProjectPledgeID)
	{
		$returnValue = null;

		$databaseConnection = $this->connect();

		$queryText = "select * from UserProjectPledge where UserProjectPledgeID = ?";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $userProjectPledgeID);
		$queryCommand->execute();
		$queryCommand->bind_result($userProjectPledgeID, $userID, $projectID, $pledgeAmountInUsd);

		while ($queryCommand->fetch())
		{
			$returnValue = new userProjectPledge($userProjectPledgeID, $userID, $projectID, $pledgeAmountInUsd);
			break;
		}

		$databaseConnection->close();

		return $returnValue;
	}

	public function userProjectPledgesGetByProjectID($projectID)
	{
		$returnValues = array();

		$databaseConnection = $this->connect();

		$queryText = "select * from UserProjectPledge where ProjectID = ? and IsActive = true";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $projectID);
		$queryCommand->execute();
		$queryCommand->bind_result(
			$userProjectPledgeID, $userID, $projectID, $pledgeAmountInUsd, $timePledged, $isActive
		);

		while ($queryCommand->fetch())
		{
			$pledge = new UserProjectPledge(
				$userProjectPledgeID, $userID, $projectID, $pledgeAmountInUsd, $timePledged, $isActive
			);
			$returnValues[] = $pledge;
		}

		$databaseConnection->close();

		return $returnValues;
	}

	public function userProjectPledgesSumGetByProjectID($projectID)
	{
		$returnValues = array();

		$databaseConnection = $this->connect();

		$queryText = "select sum(PledgeAmountInUsd) from UserProjectPledge where ProjectID = ? and IsActive = true";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $projectID);
		$queryCommand->execute();
		$queryCommand->bind_result($sum);
		$queryCommand->fetch();

		if ($sum == null)
		{
			$sum = 0;
		}

		$databaseConnection->close();

		return $sum;
	}

	public function userProjectPledgesGetByUserID($userID)
	{
		$returnValues = array();

		$databaseConnection = $this->connect();

		$queryText = "select * from UserProjectPledge where UserID = ? and IsActive = true order by ProjectID";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $userID);
		$queryCommand->execute();
		$queryCommand->bind_result(
			$userProjectPledgeID, $userID, $projectID, $pledgeAmountInUsd, $timePledged, $isActive
		);

		while ($row = $queryCommand->fetch())
		{
			$pledge = new UserProjectPledge(
				$userProjectPledgeID, $userID, $projectID, $pledgeAmountInUsd, $timePledged, $isActive
			);
			$returnValues[] = $pledge;
		}

		$databaseConnection->close();

		return $returnValues;
	}

	public function userProjectPledgesCountGetByUserID($userID)
	{
		$databaseConnection = $this->connect();

		$queryText = "select count('x') from UserProjectPledge where UserID = ? and IsActive = true";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $userID);
		$queryCommand->execute();
		$queryCommand->bind_result($count);
		$queryCommand->fetch();

		$databaseConnection->close();

		return $count;
	}

	public function userProjectPledgesGetByUserIDAndProjectID($userID, $projectID)
	{
		$returnValues = array();

		$databaseConnection = $this->connect();

		$queryText = "select * from UserProjectPledge where UserID = ? and ProjectID = ? and IsActive = true";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("ii", $userID, $projectID);
		$queryCommand->execute();
		$queryCommand->bind_result(
			$userProjectPledgeID, $userID, $projectID, $pledgeAmountInUsd, $timePledged, $isActive
		);

		while ($queryCommand->fetch() )
		{
			$pledge = new UserProjectPledge(
				$userProjectPledgeID, $userID, $projectID, $pledgeAmountInUsd, $timePledged, $isActive
			);
			$returnValues[] = $pledge;
		}

		$databaseConnection->close();

		return $returnValues;
	}

	public function userProjectPledgeSave($userProjectPledge)
	{
		$databaseConnection = $this->connect();

		if ($databaseConnection->connect_error)
		{
			die("Could not connect to database.");
		}

		$pledgesForUserAndProjectExisting =
			$this->userProjectPledgesGetByUserIDAndProjectID(
				$userProjectPledge->UserID, $UserProjectPledge->ProjectID
			);

		foreach ($pledgesForUserAndProjectExisting as $pledge)
		{
			$queryText = "update UserProjectPledge IsActive = false where UserProjectPledgeID = ?";
			$queryCommand = mysqli_prepare($databaseConnection, $queryText);
			$queryCommand->bind_param
			(
				"i",
				$userProjectPledge->userProjectPledgeID,
			);

		}

		if ($userProjectPledge->userProjectPledgeID == null)
		{
			$queryText =
				"insert into UserProjectPledge (UserID, ProjectID, PledgeAmountInUsd, TimePledged, IsActive)"
				. " values (?, ?, ?, ?, ?)";
			$queryCommand = mysqli_prepare($databaseConnection, $queryText);
			$queryCommand->bind_param
			(
				"iidsb",
				$userProjectPledge->userID,
				$userProjectPledge->projectID,
				$userProjectPledge->pledgeAmountInUsd,
				$userProjectPledge->timePledged,
				$userProjectPledge->isActive
			);
		}
		else
		{
			$queryText = "update UserProjectPledge set UserID = ?, ProjectID = ?, PledgeAmountInUsd = ?, TimePledged = ?, IsActive = ? where UserProjectPledgeID = ?";
			$queryCommand = mysqli_prepare($databaseConnection, $queryText);
			$queryCommand->bind_param
			(
				"iidsbi",
				$userProjectPledge->userID,
				$userProjectPledge->projectID,
				$userProjectPledge->pledgeAmountInUsd,
				$userProjectPledge->timePledged,
				$userProjectPledge->isActive,
				$userProjectPledge->userProjectPledgeID,
			);
		}
		$didSaveSucceed = $queryCommand->execute();

		if ($didSaveSucceed == false)
		{
			die("Could not write to database.");
		}
		else
		{
			$userProjectPledgeID = mysqli_insert_id($databaseConnection);
			if ($userProjectPledgeID != null)
			{
				$userProjectPledge->userProjectPledgeID = $userProjectPledgeID;
			}
		}

		$databaseConnection->close();

		return $userProjectPledge;
	}

}

?>
