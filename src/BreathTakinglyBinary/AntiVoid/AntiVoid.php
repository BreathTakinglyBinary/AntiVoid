<?php

declare(strict_types=1);

namespace BreathTakinglyBinary\AntiVoid;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class AntiVoid extends PluginBase implements Listener{

	/** @var string[] */
	private $enabledWorlds = [];

	/** @var string[] */
	private $disabledWorlds = [];

	/** @var bool */
	private $useDefaultWorld = false;

	public function onEnable() : void{
		$this->enabledWorlds = $this->getConfig()->get("enabled-worlds");
		$this->enabledWorlds = $this->getConfig()->get("disabled-worlds");
		$this->useDefaultWorld = $this->getConfig()->get("use-default-world");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onDamage(EntityDamageEvent $event) : void{
		$entity = $event->getEntity();
		if(!$entity instanceof Player){
			return;
		}

		$level = $entity->getLevel()->getFolderName();
		if($event->getCause() === EntityDamageEvent::CAUSE_VOID){
			if(empty($this->enabledWorlds) and empty($this->disabledWorlds)){
				$this->savePlayerFromVoid($entity);
				$event->setCancelled();
			} elseif(in_array($level, $this->enabledWorlds) and !in_array($level, $this->disabledWorlds)){
				$this->savePlayerFromVoid($entity);
				$event->setCancelled();
			} elseif(in_array($level, $this->enabledWorlds) and !(in_array($level, $this->enabledWorlds) and in_array($level, $this->disabledWorlds))){
				$this->savePlayerFromVoid($entity);
				$event->setCancelled();
			}
		}
	}

	private function savePlayerFromVoid(Player $player) : void{
		if($this->useDefaultWorld){
			$position = $player->getServer()->getDefaultLevel()->getSpawnLocation();
		} else {
			$position = $player->getLevel()->getSpawnLocation();
		}
		$player->teleport($position);
	}
}
