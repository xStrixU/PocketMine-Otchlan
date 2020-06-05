<?php

declare(strict_types=1);

namespace otchlan\fakeinventory;

use pocketmine\Player;
use otchlan\utils\MessageUtil;
use otchlan\manager\OtchlanManager;

class OtchlanInventory extends FakeInventory {
	
	private $page;
	
	public function __construct(int $size, int $page) {
		$this->page = $page;
		parent::__construct(MessageUtil::formatConfigMessage("otchlan-title", $this), $size);
	}
	
	public function getPage() : int {
		return $this->page;
	}
	
	public function updateTitle() : void {
		$this->title = MessageUtil::formatConfigMessage("otchlan-title", $this);
	}
	
	public function openFor(Player $player) : void {
		if(!OtchlanManager::started())
		 return;
		
		parent::openFor($player);
	}
}