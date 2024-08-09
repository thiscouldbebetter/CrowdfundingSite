<?php

class Project
{
	public $projectID;
	public $userIDOrganizer;
	public $name;
	public $goalInUsd;
	public $timeProposed;
	public $isActive;
	public $description;

	public function __construct($projectID, $userIDOrganizer, $name, $goalInUsd, $timeProposed, $isActive, $description)
	{
		$this->projectID = $projectID;
		$this->userIDOrganizer = $userIDOrganizer;
		$this->name = $name;
		$this->goalInUsd = $goalInUsd;
		$this->timeProposed = $timeProposed;
		$this->isActive = $isActive;
		$this->description = $description;
	}
}

?>
