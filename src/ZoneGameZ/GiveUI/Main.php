<?php

namespace ZoneGameZ\GiveUI;

use pocketmine\Server;
use pocketmine\Player;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class Main extends PluginBase implements Listener{
	
	public $playerList = [];

   public function onEnable(){
   	$this->getServer()->getPluginManager()->registerEvents($this, $this);
       $this->getLogger()->info("§bEnable Plugin GiveUI by ZoneGameZ");
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->getResource("config.yml");
       }
       
   public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool {
       switch($cmd->getName()) {
             case "giveui":
                 if($sender instanceof Player){
                    $this->Form($sender);
                 } else {
                    	$sender->sendMessage("Command-ingame");
                 }
       }
       return true;
   }
   public function Form(Player $sender){ 
   	$list = [];
   foreach($this->getServer()->getOnlinePlayers() as $p){
   	$list[] = $p->getName();
   $this->playerList[$sender->getName()] = $list;
   	}
       $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
       $f = $api->createCustomForm(function (Player $sender, array $data = null){
       	$result = $data;
           if($result === null){
               return true;
           }             
               $index = $data[0];
               $item = $data[1];
               $amount = $data[2];
               $name = $this->playerList[$sender->getName()][$index];
			   $this->getServer()->dispatchCommand($sender, "give $name $item $amount");
			   $sender->sendMessage("§eGive $item amount $amount to $name");
           });
           $f->setTitle("GiveUI");
           $f->addDropdown($this->getConfig()->get("Dropdown-Message"), $this->playerList[$sender->getName()]);
           $f->addInput($this->getConfig()->get("Input-Message"));
           $f->addSlider($this->getConfig()->get("Slider-Message"), 0, 64, 1);
           $f->sendToPlayer($sender);
           return $f;
   }
}
