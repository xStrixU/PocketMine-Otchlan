<?php

declare(strict_types=1);

namespace otchlan\manager;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\entity\object\ItemEntity;
use pocketmine\item\Item;
use otchlan\Main;
use otchlan\task\{
	OtchlanCountdowningTask, StopOtchlanTask
};
use otchlan\fakeinventory\OtchlanInventory;
use otchlan\utils\MessageUtil;

class OtchlanManager {
	
	private static $started = false;
	private static $task = null;
	private static $inventories = [];
	
	public static function init() : void {
		self::createOtchlanInventory();
		self::startCountdowning();
	}
	
	public static function createOtchlanInventory(int $page = 0) : OtchlanInventory {
		$config = Main::getInstance()->getConfig();
		$size = strtolower($config->get("otchlan-size")) == "small" ? OtchlanInventory::SMALL : OtchlanInventory::BIG;
		
		$inventory = new OtchlanInventory($size, $page);
		
		$previousPageItemSlot = (int)$config->get("previous-page-item-slot") - 1;
		$nextPageItemSlot = (int)$config->get("next-page-item-slot") - 1;
		
		$inventory->setItem($previousPageItemSlot, self::getPreviousPageItem());
		$inventory->setItem($nextPageItemSlot, self::getNextPageItem());
		 
		 self::$inventories[$page] = $inventory;
		 
		 self::updateInventoriesTitle();
		 
		 return $inventory;
	}
	
	public static function getLastInventory() {
		return end(self::$inventories);
	}
	
	public static function updateInventoriesTitle() : void {
		foreach(self::$inventories as $inventory)
		 $inventory->updateTitle();
	}
	
	public static function started() : bool {
		return self::$started;
	}
	
	public static function setStarted(bool $started = true) : void {
		self::$started = $started;
	}
	
	public static function getTask() : ?Task {
		return self::$task;
	}
	
	public static function getInventory(int $page = 0) :?OtchlanInventory {
		return self::$inventories[$page] ?? null;
	}
	
	public static function start() : void {
		if(self::$started)
		 return;
		
		self::$started = true;
		self::otchlanProcess();
		Main::getInstance()->getScheduler()->scheduleDelayedTask(self::$task = new StopOtchlanTask(), 20*((int) Main::getInstance()->getConfig()->get("stop-time")));
	}
	
	public static function otchlanProcess() : void {
		
		foreach(Server::getInstance()->getLevels() as $level) {
			foreach($level->getEntities() as $entity) {
				if($entity instanceof ItemEntity) {
					$item = $entity->getItem();
					$entity->close();
					
					if(self::getInventory()->canAddItem($item)) {
						self::getInventory()->addItem($item);
					} else {
						for($i = 1;;$i++) {
							$inventory = null;
							
							if(self::getInventory($i) === null) {
							 $inventory = self::createOtchlanInventory($i);
							} else {
								 $inventory = self::getInventory($i);
							}
							
							if($inventory->canAddItem($item)) {
								$inventory->addItem($item);
								break;
							}
						}
					}
				}
			}
		}
	}
	
	public static function stop() : void {
		if(!self::$started)
		 return;
		
		self::$started = false;
		foreach(self::$inventories as $inventory) {
			foreach($inventory->getViewers() as $viewer) {
				$inventory->closeFor($viewer);
			}
		}
		self::$inventories = [];
		self::$inventories[0] = self::createOtchlanInventory();
		self::startCountdowning();
	}
	
	public static function startCountdowning() : void {
		if(self::$started)
		 return;
		
		Main::getInstance()->getScheduler()->scheduleRepeatingTask(self::$task = new OtchlanCountdowningTask(), 20);
	}
	
	public static function getPreviousPageItem() : Item {
		$config = Main::getInstance()->getConfig();
		
		$itemData = explode(':',$config->get("previous-page-item"));
		
		$item = Item::get((int)$itemData[0], (int)$itemData[1], (int)$itemData[2]);
		$item->setCustomName(MessageUtil::formatMessage($config->get("previous-page-item-name"), false));
		
		return $item;
	}
	
	public static function getNextPageItem() : Item {
		$config = Main::getInstance()->getConfig();
		
		$itemData = explode(':',$config->get("next-page-item"));
		
		$item = Item::get((int)$itemData[0], (int)$itemData[1], (int)$itemData[2]);
		$item->setCustomName(MessageUtil::formatMessage($config->get("next-page-item-name"), false));
		
		return $item;
	}
}