<?php

declare(strict_types=1);

namespace otchlan\utils;

use otchlan\Main;
use otchlan\manager\OtchlanManager;
use otchlan\task\OtchlanCountdowningTask;
use otchlan\fakeinventory\OtchlanInventory;

class MessageUtil {
	
	public static function formatMessage(string $message, bool $prefix = true, ?array $words = null) : string {
		$config = Main::getInstance()->getConfig();
		
		$prefixFormat = $config->get("message-prefix");
		$color = $config->get("message-color");
		$markWordsColor = $config->get("message-mark-words-color");
		
		$message = $message.$color;
		
		if($prefix)
		 $message = $prefixFormat." ".$color.$message;
		
		if($words !== null) {
		 foreach($words as $word)
			 $message = str_replace($word, $markWordsColor.$word.$color, $message);
		}
		
		return self::fixColors($message);
	}
	
	public static function fixColors(string $message) : string {
		return str_replace('&', '§', $message);
	}
	
	public static function formatConfigMessage(string $message, ?OtchlanInventory $otchlanInventory = null) : string {
		$message = Main::getInstance()->getConfig()->get($message);
		
		$data = explode(';', $message);
		
		$prefix = $data[0] == "true" ? true : false;
		$message = (string) $data[1];
	 	
	 unset($data[0], $data[1]);
	
		$words = $data;
	
		foreach($words as $key => $value) {
	 	$words[$key] = self::formatConstValues($value);
		}
		
		$message = self::formatConstValues($message, $otchlanInventory);
		
		return self::formatMessage($message, $prefix, $words);
	}
	
	public static function formatConstValues(string $message, ?OtchlanInventory $otchlanInventory = null) : string {
		$task = OtchlanManager::getTask();
		
		$countdownTime = 0;
		
		if($task instanceof OtchlanCountdowningTask) {
			$countdownTime = $task->getCountdownTime();
		}
		
		$message = str_replace("{COUNTDOWN_TIME}", $countdownTime, $message);
		
		if($otchlanInventory !== null)
		 $message = str_replace("{PAGE}", ($otchlanInventory->getPage()+1), $message);
		
		$lastPage = 1;
		
		$lastInv = OtchlanManager::getLastInventory();
		
		if($lastInv !== false)
		 $lastPage = $lastInv->getPage()+1;
		
		$message = str_replace("{LAST_PAGE}", $lastPage, $message);
		
		return $message;
	}
}