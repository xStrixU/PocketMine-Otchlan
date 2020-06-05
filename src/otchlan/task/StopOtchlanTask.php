<?php

declare(strict_types=1);

namespace otchlan\task;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use otchlan\utils\MessageUtil;
use otchlan\manager\OtchlanManager;

class StopOtchlanTask extends Task {
	
	public function onRun(int $currentTick) : void {
		OtchlanManager::stop();
		Server::getInstance()->broadcastMessage(MessageUtil::formatConfigMessage("message-otchlan-stop"));
	}
}