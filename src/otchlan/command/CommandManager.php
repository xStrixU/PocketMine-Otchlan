<?php

declare(strict_types=1);

namespace otchlan\command;

use pocketmine\Server;

class CommandManager {
	
	public static function init() : void {
		Server::getInstance()->getCommandMap()->register("otchlan", new OtchlanCommand());
	}
}