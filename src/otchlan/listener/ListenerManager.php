<?php

declare(strict_types=1);

namespace otchlan\listener;

use pocketmine\Server;
use otchlan\Main;

class ListenerManager {
	
	public static function init() : void {
		$listeners = [
		 new InventoryTransactionListener()
		];
		
		foreach($listeners as $listener)
		 Server::getInstance()->getPluginManager()->registerEvents($listener, Main::getInstance());
	}
}