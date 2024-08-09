<?php

class Project
{
	public $projectID;
	public $userIDOrganizer;
	public $name;
	public $goalInUsd;
	public $isActive;
	public $description;
	public $dateProposed;

	public function __construct($projectID, $userIDOrganizer, $name, $goalInUsd, $isActive, $description, $dateProposed)
	{
		$this->projectID = $projectID;
		$this->userIDOrganizer = $userIDOrganizer;
		$this->name = $name;
		$this->goalInUsd = $goalInUsd;
		$this->isActive = $isActive;
		$this->description = $description;
		$this->dateProposed = $dateProposed;
	}
}

?>
