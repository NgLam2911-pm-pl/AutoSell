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

class main extends PluginBase implements Listener
{
	private $mode = [];
	public function onEnable()
	{
        $this->getLogger()->info(TextFormat::GREEN . "Plugin is enabled! [Plugin by LamPocketVN/edited by esh123cookie]");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
		
    }
	public function onDisable ()
	{
		$this->getLogger()->info(TextFormat::RED . "Plugin has stopped !");
	}
	public function onJoin (PlayerJoinEvent $j)
	{
	    $player = $j->getPlayer()->getName();
		$this->mode[$player] = "off";
	}
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
       if (strtolower($cmd->getName()) == "autosell") {
           if(!isset($args[0])){
               $sender->sendMessage("§l§7[§3AutoSell§7]§a Usage: /autosell <on|off>");
               return false;
           }
           switch ($args[0]) {
               case "on":
			       $sender->sendMessage("§l§7[§3AutoSell§7]§a Autosell enabled ");
				   $this->mode[$sender->getName()] = "on";
				   break;

               case "off":
			       $sender->sendMessage("§l§7[§3AutoSell§7]§c Autosell disabled "); 
                   $this->mode[$sender->getName()] = "off";
				   break;
               default :
                   $sender->sendMessage("§l§b[§6AutoSell§b]§a Usage: /autosell <on|off>");
                   break;
           }
       }

       return true;
   }
    public function onBreak(BlockBreakEvent $event) : void {
		$player = $event->getPlayer();
		foreach($event->getDrops() as $drop) {
			if(!$player->getInventory()->canAddItem($drop)) 
			{
				if ($this->mode[$player->getName()] == "on") 
				{
				$this->getServer()->dispatchCommand($player, "rca sell inv");
				$player->sendMessage("§l§b[§6AutoSell§b]§a Automatically sold items in your inv!");
				}
				break;
			}
		}
	}
    public function onQuit(PlayerQuitEvent $e){
       $a = "autosell off";
       $this->getServer()->dispatchCommand($e->getPlayer(),$a);
    }
}
