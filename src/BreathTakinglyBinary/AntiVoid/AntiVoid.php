<?php

declare(strict_types=1);

namespace BreathTakinglyBinary\AntiVoid;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class AntiVoid extends PluginBase implements Listener{

	public function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onDamage(EntityDamageEvent $event) : void{
		$entity = $event->getEntity();
		if(!$entity instanceof Player){
			return;
		}

		if($event->getCause() === EntityDamageEvent::CAUSE_VOID){
			$entity->teleport($entity->getLevel()->getSafeSpawn());
			$event->setCancelled();
		}
	}
}
