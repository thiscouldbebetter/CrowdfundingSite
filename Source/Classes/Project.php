<?php

class Project
{
	public $projectID;
	public $userIDOrganizer;
	public $name;
	public $goalInUsd;
	public $isActive;
	public $dateProposed;
	public $description;

	public function __construct($projectID, $userIDOrganizer, $name, $goalInUsd, $isActive, $dateProposed, $description)
	{
		$this->projectID = $projectID;
		$this->userIDOrganizer = $userIDOrganizer;
		$this->name = $name;
		$this->goalInUsd = $goalInUsd;
		$this->isActive = $isActive;
		$this->dateProposed = $dateProposed;
		$this->description = $description;
	}
}

?>
