<?php

namespace LamPocketVN\AutoSell;

use pocketmine\{Player, Server};
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\{Command,CommandSender, CommandExecutor, ConsoleCommandSender};
use pocketmine\inventory\BaseInventory;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\utils\TextFormat;
use pocketmine\utils\config;

class AutoSell extends PluginBase implements Listener
{
	public $config;
	
	public $mode = [];
	
	public function onEnable()
	{
		$this->saveResource("setting.yml");
		$this->config = new Config($this->getDataFolder() . "setting.yml", Config::YAML);
		$this->set = $this->config->getAll();
		
        $this->getLogger()->info(TextFormat::GREEN . "Plugin enabled ! [Plugin by LamPocketVN]");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);	
    }
	
	public function onDisable ()
	{
		$this->getLogger()->info(TextFormat::RED . "Plugin disabled !");
	}
	
	
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool
	{
		if (!$sender instanceof Player)
		{
			$sender->sendMessage("Please use in game !");
			return true;
		}
		if (strtolower($cmd->getName()) == "autosell") 
	   {
           if(!isset($args[0]))
		   {
               $sender->sendMessage($this->set["usage"]);
               return false;
           }
           switch ($args[0]) 
		   {
			   case "on":
			       if (!$this->isAutoSell($sender))
				   {
					   $sender->sendMessage($this->set["enabled"]);
				       $this->mode[$sender->getName()] = "on";
				   }
			       else $sender->sendMessage($this->set["has-enabled"]);
				   break;
               case "off":
			       if ($this->isAutoSell($sender))
				   {
					   $sender->sendMessage($this->set["disabled"]); 
                       $this->mode[$sender->getName()] = "off";
				   }
			       else $sender->sendMessage($this->set["has-disabled"]);
				   break;
               default :
                   $sender->sendMessage($this->set["usage"]);
                   break;
           }
       }

       return true;
   }
//============================== Event Listener ==============================
    public function onBreak(BlockBreakEvent $event)
	{
		$player = $event->getPlayer();
		foreach($event->getDrops() as $drop) {
			if(!$player->getInventory()->canAddItem($drop)) 
			{
				if ($this->isAutoSell($player))
				{
					$this->getServer()->dispatchCommand($player, $this->set["sellcmd"]);
					$player->sendMessage($this->set["sell"]);
				}
				break;
			}
		}
	}
	
	public function onJoin (PlayerJoinEvent $event)
	{
	    $player = $event->getPlayer()->getName();
		$this->mode[$player] = "off";
	}
//=============================== API ========================================

	public function isAutoSell($player): bool
	{
		if ($this->mode[$player->getName()] === "on") 
		{
			return true;
		}
		else 
			return false;
	}
}
