<?php

namespace HimmelKreis4865\BetterAC\Listeners;

use HimmelKreis4865\BetterAC\BetterAC;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\cheat\PlayerIllegalMoveEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector3;
use pocketmine\Player;

class HitListeners implements Listener
{

    private $moves = [];

    public function onHit(EntityDamageByEntityEvent $event) {
        if (!$event->getDamager() instanceof Player) return;
        if (BetterAC::getInstance()->configManager->reachCheckEnabled) {
            if (!BetterAC::getInstance()->inRange($event->getDamager(), $event->getEntity()->asVector3(), BetterAC::getInstance()->configManager->maxRange[BetterAC::getInstance()->playerClientDataList[$event->getDamager()->getName()]])) {
                BetterAC::getInstance()->warnPlayer($event->getDamager());
                $event->setCancelled();
                return;
            }
            if (isset(BetterAC::getInstance()->lastMoveUpdates[$event->getDamager()->getName()])) {
                if (!BetterAC::getInstance()->inRange($event->getEntity(), BetterAC::getInstance()->lastMoveUpdates[$event->getDamager()->getName()], BetterAC::getInstance()->configManager->maxRange[BetterAC::getInstance()->playerClientDataList[$event->getDamager()->getName()]])) {
                    BetterAC::getInstance()->warnPlayer($event->getDamager());
                    $event->setCancelled();
                    return;
                }
            }

        }
        if (!BetterAC::getInstance()->configManager->autoClickerCheckEnabled) return;
        if (BetterAC::getInstance()->reachedTPSLimit()) return;
        if ($event->getDamager() instanceof Player) BetterAC::getInstance()->checkClickRate($event->getDamager());
    }




    public function onInteract(PlayerInteractEvent $event) {
        if (!BetterAC::getInstance()->configManager->autoClickerCheckEnabled) return;
        if (BetterAC::getInstance()->reachedTPSLimit()) return;
        BetterAC::getInstance()->checkClickRate($event->getPlayer());
    }
}