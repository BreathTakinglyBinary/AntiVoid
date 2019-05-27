<?php

declare(strict_types=1);

namespace BreathTakinglyBinary\AntiVoid;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\level\Level;
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
		if($event->getCause() === EntityDamageEvent::CAUSE_VOID){
			if($this->saveFromVoidAllowed($entity->getLevel())){
				$this->savePlayerFromVoid($entity);
				$event->setCancelled();
			}
		}
	}

	/**
	 * @param Level $level
	 *
	 * @return bool
	 */
	private function saveFromVoidAllowed(Level $level) : bool {
		if(empty($this->enabledWorlds) and empty($this->disabledWorlds)){
			return true;
		}
		$levelFolderName = $level->getFolderName();

		if(in_array($levelFolderName, $this->disabledWorlds)){
			return false;
		}
		if(in_array($levelFolderName, $this->enabledWorlds)){
			return true;
		}
		if(!empty($this->enabledWorlds) and !in_array($levelFolderName, $this->enabledWorlds)){
			return false;
		}
		return true;
	}

	/**
	 * @param Player $player
	 */
	private function savePlayerFromVoid(Player $player) : void{
		if($this->useDefaultWorld){
			$position = $player->getServer()->getDefaultLevel()->getSpawnLocation();
		} else {
			$position = $player->getLevel()->getSpawnLocation();
		}
		$player->teleport($position);
	}
}
