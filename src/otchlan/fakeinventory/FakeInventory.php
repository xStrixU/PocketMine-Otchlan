<?php

declare(strict_types=1);

namespace otchlan\fakeinventory;

use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\block\BlockFactory;
use pocketmine\inventory\ContainerInventory;
use pocketmine\nbt\{
	NetworkLittleEndianNBTStream, tag\CompoundTag
};
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use otchlan\fakeinventory\task\SendFakeInventoryWindowTask;
use otchlan\Main;

class FakeInventory extends ContainerInventory {
	
	public const SMALL = 27;
	public const BIG = 54;
	
	protected $holder;
	protected $title;
	protected $size;
	
	public function __construct(string $title = "Fake Inventory", int $size = 54) {
		$holder = new Vector3();
		
		parent::__construct($holder, [], $size, $title);
		
		$this->holder = $holder;
		$this->title = $title;
		$this->size = $size;
	}
	
	public function openFor(Player $player) : void {
		$pos = $player->floor()->add(0,2);
		
		$this->holder = new Vector3($pos->x, $pos->y, $pos->z);
		
		$pk = new UpdateBlockPacket();
  $pk->x = $pos->x;
  $pk->y = $pos->y;
  $pk->z = $pos->z;
  $pk->flags = UpdateBlockPacket::FLAG_ALL;
  $pk->blockRuntimeId = BlockFactory::toStaticRuntimeId(54);
  
  $player->dataPacket($pk);
  
  if($this->size == self::BIG) {
  	$pairPos = $pos->add(1);
   	
  	$pk = new UpdateBlockPacket();
   $pk->x = $pairPos->x;
   $pk->y = $pairPos->y;
   $pk->z = $pairPos->z;
   $pk->flags = UpdateBlockPacket::FLAG_ALL;
   $pk->blockRuntimeId = BlockFactory::toStaticRuntimeId(54);
   $player->dataPacket($pk);
			
			$tag = new CompoundTag();
   $tag->setInt('pairx', $pos->x);
   $tag->setInt('pairz', $pos->z);
   
   $writer = new NetworkLittleEndianNBTStream();
   $pk = new BlockActorDataPacket;
   $pk->x = $pairPos->x;
   $pk->y = $pairPos->y;
   $pk->z = $pairPos->z;
   $pk->namedtag = $writer->write($tag);
   $player->dataPacket($pk);
  }
  
  $writer = new NetworkLittleEndianNBTStream();
  
  $pk = new BlockActorDataPacket;
  $pk->x = $pos->x;
  $pk->y = $pos->y;
  $pk->z = $pos->z;
  
  $tag = new CompoundTag();
  $tag->setString('CustomName', $this->title);
  $pk->namedtag = $writer->write($tag);
  $player->dataPacket($pk);
  
  Main::getInstance()->getScheduler()->scheduleDelayedTask(new SendFakeInventoryWindowTask($player, $this), 8);
  FakeInventoryAPI::setInventory($player, $this);
 }
 
 public function onClose(Player $who) : void {
 	$this->closeFor($who);
  parent::onClose($who);
 }
 
 public function closeFor(Player $player) : void {
 	$pos = $player->floor()->add(0,2);
 	
 	$block = $player->getLevel()->getBlock($pos);
 	
 	$pk1 = new UpdateBlockPacket();
  $pk1->x = $pos->x;
  $pk1->y = $pos->y;
  $pk1->z = $pos->z;
  $pk1->flags = UpdateBlockPacket::FLAG_ALL;
  $pk1->blockRuntimeId = BlockFactory::toStaticRuntimeId($block->getId(), $block->getDamage());
  
  if($this->size == self::BIG) {
  	$pos = $pos->add(1);
  	$pk2 = clone $pk1;
  	$pk2->x = $pos->x;
  	$pk2->y = $pos->y;
  	$pk2->z = $pos->z;
  	$player->dataPacket($pk2);
  }
  
  $player->dataPacket($pk1);
  
  FakeInventoryAPI::unsetInventory($player);
 }
 
 public function getNetworkType() : int {
  return WindowTypes::CONTAINER;
 }
 
 public function getName() : string {
  return "Fake Inventory";
 }
 
 public function getTitle() : string {
 	return $this->title;
 }
 
 public function setTitle(string $title) : void {
 	$this->title = $title;
 }
 
 public function getDefaultSize() : int {
  return $this->size;
 }
 
 public function getHolder() : Vector3 {
  return $this->holder;
 }
}