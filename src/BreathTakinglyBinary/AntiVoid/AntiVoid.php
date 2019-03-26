<?php

declare(strict_types=1);

namespace BreathTakinglyBinary\AntiVoid;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class AntiVoid extends PluginBase implements Listener{

	public function onEnable() : void{
		@mkdir($this->getDataFolder());
		$this->saveResource("config.yml");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onDamage(EntityDamageEvent $event) : void{
		$entity = $event->getEntity();
		if(!$entity instanceof Player){
			return;
		}
		$level = $entity->getLevel()->getName();
		if($event->getCause() === EntityDamageEvent::CAUSE_VOID){
			if(in_array($level, $this->getConfig()->get("enabled-worlds", [])) and !in_array($level, $this->getConfig()->get("disabled-worlds", [])) or $this->getConfig()->get("enabled-worlds") != null){
				$entity->teleport($entity->getLevel()->getSafeSpawn());
				$event->setCancelled();
			}
		}
	}
}
