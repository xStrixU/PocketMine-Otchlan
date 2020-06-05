<?php

declare(strict_types=1);

namespace otchlan\fakeinventory\task;

use pocketmine\scheduler\Task;
use pocketmine\Player;
use otchlan\fakeinventory\FakeInventory;

class OpenFakeInventoryTask extends Task {
	
	private $player;
	private $inventory;
	
	public function __construct(Player $player, FakeInventory $inventory) {
		$this->player = $player;
		$this->inventory = $inventory;
	}
	
	public function onRun(int $currentTick) : void  {
		$this->inventory->openFor($this->player);
	}
}