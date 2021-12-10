<?php

declare(strict_types=1);

namespace BreathTakinglyBinary\AntiVoid;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\world\World;

class AntiVoid extends PluginBase implements Listener{

	/** @var string[] */
	private $enabledWorlds = [];

	/** @var string[] */
	private $disabledWorlds = [];

	/** @var bool */
	private $useDefaultWorld = false;

	public function onEnable() : void{
		$this->enabledWorlds = $this->getConfig()->get("enabled-worlds");
		$this->getLogger()->info("§5Enabled Worlds: " . implode(", ", $this->enabledWorlds));
		$this->disabledWorlds = $this->getConfig()->get("disabled-worlds");
		$this->getLogger()->info("§5Disabled Worlds: " . implode(", ", $this->disabledWorlds));
		$this->useDefaultWorld = $this->getConfig()->get("use-default-world");
		$msg = "§5Use Default World: ";
		$msg .= $this->useDefaultWorld ? "TRUE" : "FALSE";
		$this->getLogger()->info($msg);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onDamage(EntityDamageEvent $event) : void{
		$entity = $event->getEntity();
		if(!$entity instanceof Player){
			return;
		}
		if($event->getCause() === EntityDamageEvent::CAUSE_VOID){
			if($this->saveFromVoidAllowed($entity->getWorld())){
				$this->savePlayerFromVoid($entity);
				$event->cancel();
			}
		}
	}

	/**
	 * @param World $world
	 *
	 * @return bool
	 */
	private function saveFromVoidAllowed(World $world) : bool {
		if(empty($this->enabledWorlds) and empty($this->disabledWorlds)){
			return true;
		}
		$levelFolderName = $world->getFolderName();

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
			$position = $player->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation();
		} else {
			$position = $player->getWorld()->getSpawnLocation();
		}
		$player->teleport($position);
	}
}
