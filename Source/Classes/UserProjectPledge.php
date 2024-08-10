<?php

class UserProjectPledge
{
	public $userProjectPledgeID;
	public $userID;
	public $projectID;
	public $pledgeAmountInUsd;
	public $timePledged;
	public $isActive;

	public function __construct(
		$userProjectPledgeID, $userID, $projectID, $pledgeAmountInUsd, $timePledged, $isActive
	)
	{
		$this->userProjectPledgeID = $userProjectPledgeID;
		$this->userID = $userID;
		$this->projectID = $projectID;
		$this->pledgeAmountInUsd = $pledgeAmountInUsd;
		$this->timePledged = $timePledged;
		$this->isActive = $isActive;
	}

}

?>
