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
		$queryCommand->bind_result($projectID, $userIDOrganizer, $name, $goalInUsd, $isActive, $dateProposed, $description);

		while ($queryCommand->fetch())
		{
			$returnValue = new Project($projectID, $userIDOrganizer, $name, $goalInUsd, $isActive, $dateProposed, $description);
			break;
		}

		$databaseConnection->close();

		return $returnValue;
	}

	public function projectsGetAll()
	{
		$returnValues = array();

		$databaseConnection = $this->connect();

		$queryText = "select * from Project";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->execute();
		$queryCommand->bind_result($projectID, $name, $goalInUsd, $isActive, $description);

		while ($queryCommand->fetch())
		{
			$project = new Project($projectID, $name, $goalInUsd, $isActive, $description);
			$returnValues[$projectID] = $project;
		}

		$databaseConnection->close();

		return $returnValues;
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
		$queryCommand->bind_result($projectID, $userIDOrganizer, $name, $goalInUsd, $isActive, $dateProposed, $description);

		while ($queryCommand->fetch())
		{
			$project = new Project($projectID, $userIDOrganizer, $name, $goalInUsd, $isActive, $dateProposed, $description);
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
		$queryCommand->bind_result($userID, $username, $emailAddress, $passwordSalt, $passwordHashed, $passwordResetCode, $isActive);
		$queryCommand->store_result();

		$numberOfRows = $queryCommand->num_rows();
		if ($numberOfRows == 0)
		{
			$userFound = null;
		}
		else
		{
			$queryCommand->fetch();

			$pledges = $this->userProjectPledgesGetByUserID($userID);

			$userFound = new User
			(
				$userID, $username, $emailAddress, $passwordSalt,
				$passwordHashed, $passwordResetCode, $isActive, $pledges
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
		$returnValue = null;

		$databaseConnection = $this->connect();

		$queryText = "select * from UserProjectPledge where ProjectID = ?";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $projectID);
		$queryCommand->execute();
		$queryCommand->bind_result($userProjectPledgeID, $userID, $projectID, $pledgeAmountInUsd, $timePledged);

		while ($queryCommand->fetch())
		{
			$returnValue = new UserProjectPledge($userProjectPledgeID, $userID, $projectID, $pledgeAmountInUsd, $timePledged);
			break;
		}

		$databaseConnection->close();

		return $returnValue;
	}

	public function userProjectPledgesGetByUserID($userID)
	{
		$returnValues = array();

		$databaseConnection = $this->connect();

		$queryText = "select * from UserProjectPledge where UserID = ? order by ProjectID";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $userID);
		$queryCommand->execute();
		$queryCommand->bind_result($userProjectPledgeID, $userID, $projectID, $pledgeAmountInUsd, $timePledged);

		while ($row = $queryCommand->fetch())
		{
			$pledge = new UserProjectPledge($userProjectPledgeID, $userID, $projectID, $pledgeAmountInUsd, $timePledged);
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

		if ($userProjectPledge->userProjectPledgeID == null)
		{
			$queryText =
				"insert into UserProjectPledge (UserID, ProjectID, PledgeAmountInUsd)"
				. " values (?, ?, ?)";
			$queryCommand = mysqli_prepare($databaseConnection, $queryText);
			$queryCommand->bind_param
			(
				"iid", $userProjectPledge->userID, $userProjectPledge->projectID, $userProjectPledge->pledgeAmountInUsd
			);
		}
		else
		{
			$queryText = "update UserProjectPledge set UserID = ?, ProjectID = ?, pledgeAmountInUsd = ? where UserProjectPledgeID = ?";
			$queryCommand = mysqli_prepare($databaseConnection, $queryText);
			$queryCommand->bind_param
			(
				"iidi", $userProjectPledge->userID, $userProjectPledge->projectID, $userProjectPledge->userProjectPledgeID
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
