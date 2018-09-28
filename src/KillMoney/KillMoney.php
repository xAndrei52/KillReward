<?php

/*
 * KillMoney - A PocketMine-MP plugin that allows you to give your players the opportunity to earn money by killing other players
 * Copyright (C) 2017 Kevin Andrews <https://github.com/kenygamer/KillMoney>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
*/

declare(strict_types=1);

namespace KillMoney;

use pocketmine\event\entity\{EntityDeathEvent, EntityDamageByEntityEvent};
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

use onebone\economyapi\EconomyAPI;

class KillMoney extends PluginBase implements Listener{
  
  public function onEnable() : void{
    if(!is_dir($this->getDataFolder())){
      @mkdir($this->getDataFolder());
    }
    $this->saveDefaultConfig();
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
  }
  
  /**
   * @param PlayerDeathEvent $event
   */

  public function onPlayerDeath(PlayerDeathEvent $event) : void{
    $victim = $event->getPlayer();
    if($victim->getLastDamageCause() instanceof EntityDamageByEntityEvent){
      if($victim->getLastDamageCause()->getDamager() instanceof Player){
        if(empty($this->getConfig()->get("worlds", [])) or in_array($victim->getLevel()->getName(), $this->getConfig()->get("worlds", []))){
          $killer = $victim->getLastDamageCause()->getDamager();    

         if(!EconomyAPI::getInstance()->addMoney($killer, $this->getConfig()->get("money", 100))){
            $this->getLogger()->error("Failed to add money due to EconomyAPI error");
            return;
          }
          if($this->getConfig()->getNested("messages.enable", true)){
            $msg = str_replace("%MONEY%", $this->getConfig()->get("money", 100), $this->getConfig()->getNested("messages.message", "§b§l(PLAYERKILL)&r §dYou have earned §5%MONEY% §dfor killing §5%PLAYER%"));
            $msg = str_replace("%PLAYER%", $victim->getName(), $msg);
            $killer->sendMessage($msg);
          }
          }
          }
          }
          }
          /**
           * @param EntityDeathEvent $event
           */
          public function onEntityDeath(EntityDeathEvent $event) : void{
          $mobvictim = $event->getEntity();
    if($mobvictim->getLastDamageCause() instanceof EntityDamageByEntityEvent){
      if($mobvictim->getLastDamageCause()->getDamager() instanceof Player){
        if(empty($this->getConfig()->get("worlds", [])) or in_array($mobvictim->getLevel()->getName(), $this->getConfig()->get("worlds", []))){
          $killerplayer = $mobvictim->getLastDamageCause()->getDamager();
        
          if(!EconomyAPI::getInstance()->addMoney($killerplayer, $this->getConfig()->get("mob-money", 50))){
            $this->getLogger()->error("Failed to add money due to EconomyAPI error");
            return;
          }
          if($this->getConfig()->getNested("messages.enable", true)){
            $message = str_replace("%MOB_MONEY%", $this->getConfig()->get("mob-money", 50), $this->getConfig()->getNested("messages.mobmessage", "§e§l(MOBKILL)§r §6You have earned §e%MOB_MONEY% §6for killing §e%MOB%"));
            $message = str_replace("%MOB%", $mobvictim->getName(), $message);
            $killerplayer->sendMessage($message);
          }
        }
      }
    }
  }
}
