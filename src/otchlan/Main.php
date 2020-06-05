<?php

declare(strict_types=1);

namespace otchlan;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use otchlan\command\CommandManager;
use otchlan\manager\OtchlanManager;
use otchlan\listener\ListenerManager;

class Main extends PluginBase {
	
	private static $instance;
	
	public const CONFIG_VERSION = 1;
	
	public static function getInstance() : Main {
		return self::$instance;
	}
	
	public function onEnable() : void {
		$this->saveResource("settings.yml");
		$this->init();
		
		if($this->getConfig()->get("config-version") != self::CONFIG_VERSION) {
			$this->getLogger()->critical("UŻYWASZ ZŁEJ WERSJI CONFIGU! ABY TO NAPRAWIĆ USUŃ PLIK settings.yml Z FOLDERU Otchlan W FOLDERZE plugin_data");
			$this->getServer()->shutdown();
		}
		
		$this->getLogger()->info("Włączono plugin!");
	}
	
	private function init() : void {
		self::$instance = $this;
		
		CommandManager::init();
		OtchlanManager::init();
		ListenerManager::init();
	}
	
	public function onDisable() : void {
		$this->getLogger()->info("Wyłączono plugin!");
	}
	
	public function getConfig() : Config {
		return new Config($this->getDataFolder(). "settings.yml", Config::YAML);
	}
}