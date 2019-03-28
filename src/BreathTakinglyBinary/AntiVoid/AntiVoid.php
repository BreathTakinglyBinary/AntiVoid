<?php

declare(strict_types=1);

namespace BreathTakinglyBinary\AntiVoid;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class AntiVoid extends PluginBase implements Listener{

	private $config;

	public function onEnable() : void{
		if(!is_dir($this->getDataFolder())){
			@mkdir($this->getDataFolder());
		}

		$this->saveDefaultConfig();
		$this->config = $this->getConfig()->getAll();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onDamage(EntityDamageEvent $event) : void{
		$entity = $event->getEntity();
		if(!$entity instanceof Player){
			return;
		}

		$level = $entity->getLevel()->getFolderName();
		if($event->getCause() === EntityDamageEvent::CAUSE_VOID){
			if(empty($this->config["enabled-worlds"]) and empty($this->config["disabled-worlds"])){
				$entity->teleport($entity->getLevel()->getSafeSpawn());
				$event->setCancelled();
			} elseif(in_array($level, $this->config["enabled-worlds"]) and !in_array($level, $this->config["disabled-worlds"])){
				$entity->teleport($entity->getLevel()->getSafeSpawn());
				$event->setCancelled();
			} elseif(in_array($level, $this->config["enabled-worlds"]) and !(in_array($level, $this->config["enabled-worlds"]) and in_array($level, $this->config["disabled-worlds"]))){
				$entity->teleport($entity->getLevel()->getSafeSpawn());
				$event->setCancelled();
			}
		}
	}
}
