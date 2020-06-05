<?php

declare(strict_types=1);

namespace otchlan\command;

use pocketmine\command\{
	Command, CommandSender
};
use otchlan\manager\OtchlanManager;
use otchlan\utils\MessageUtil;

class OtchlanCommand extends Command {
	
	public function __construct() {
		parent::__construct("otchlan", "Otwiera otchlan");
	}
	
	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!OtchlanManager::started()) {
			$sender->sendMessage(MessageUtil::formatConfigMessage("message-otchlan-not-started"));
			return;
		}
		
		OtchlanManager::getInventory()->openFor($sender);
	}
}