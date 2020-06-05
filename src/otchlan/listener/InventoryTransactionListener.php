<?php

declare(strict_types=1);

namespace otchlan\listener;

use pocketmine\event\Listener;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\item\Item;
use otchlan\fakeinventory\OtchlanInventory;
use otchlan\fakeinventory\task\OpenFakeInventoryTask;
use otchlan\manager\OtchlanManager;
use otchlan\Main;
class InventoryTransactionListener implements Listener {
	
	public function otchlanInventoryTransaction(InventoryTransactionEvent $e) : void {
		$trans = $e->getTransaction()->getActions();
 	$invs = $e->getTransaction()->getInventories();
 	$player = $e->getTransaction()->getSource();
 	
 	$item = null;
 	
 	$inventory = null;
 	
 	foreach($trans as $t) {
 		foreach($invs as $inv) {
 			if($inv instanceof OtchlanInventory) {
 				if($inventory === null)
 				 $inventory = $inv;
 				
 				if($item === null && $t->getTargetItem()->getId() !== Item::AIR)
 			 	$item = $t->getTargetItem();
 			}
 		}
 	}
 	
 	if($item !== null) {
 		if($item->equalsExact(OtchlanManager::getPreviousPageItem())) {
 			$e->setCancelled(true);
 			
 			if($inventory->getPage() > 0) {
 				$inventory->closeFor($player);
 				Main::getInstance()->getScheduler()->scheduleDelayedTask(new OpenFakeInventoryTask($player, OtchlanManager::getInventory($inventory->getPage() - 1)), 12);
 			}
 		}
 		
 		if($item->equalsExact(OtchlanManager::getNextPageItem())) {
 			$e->setCancelled(true);
 			
 			if($inventory->getPage() < OtchlanManager::getLastInventory()->getPage()) {
 				$inventory->closeFor($player);
 				
 				Main::getInstance()->getScheduler()->scheduleDelayedTask(new OpenFakeInventoryTask($player, OtchlanManager::getInventory($inventory->getPage() + 1)), 12);
 			}
 		}
 	}
	}
}