<?php

declare(strict_types=1);

namespace otchlan\task;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use otchlan\Main;
use otchlan\utils\MessageUtil;
use otchlan\manager\OtchlanManager;

class OtchlanCountdowningTask extends Task {
	
	private $countdownTime;
	
	public function __construct() {
		$this->countdownTime = (int) Main::getInstance()->getConfig()->get("countdown-time");
	}
	
	public function onRun(int $currentTick) : void {
		$server = Server::getInstance();
		
		if($this->countdownTime == 30 || $this->countdownTime == 10 || $this->countdownTime <= 5 && $this->countdownTime > 0)
		 $server->broadcastMessage(MessageUtil::formatConfigMessage("message-otchlan-countdown"));
		elseif($this->countdownTime == 0) {
			$server->broadcastMessage(MessageUtil::formatConfigMessage("message-otchlan-start"));
			$this->getHandler()->cancel();
			OtchlanManager::start();
			return;
		}
		
		$this->countdownTime--;
	}
	
	public function getCountdownTime() : int {
		return $this->countdownTime;
	}
}