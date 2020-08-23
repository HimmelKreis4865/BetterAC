<?php

namespace HimmelKreis4865\BetterAC\Listeners;

use HimmelKreis4865\BetterAC\BetterAC;
use HimmelKreis4865\BetterAC\Events\PlayerWarnEvent;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\cheat\PlayerIllegalMoveEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\Pickaxe;
use pocketmine\level\Location;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;

class HitListeners implements Listener
{
    private $maxYawDif = [
        BetterAC::TYPE_MOBILE => 180.0,
        BetterAC::TYPE_PC => 10.0,
        BetterAC::TYPE_CONSOLE => 10.0
    ];

    public function onHit(EntityDamageByEntityEvent $event) {
        if (BetterAC::getInstance()->reachedTPSLimit()) return;
        if (!$event->getDamager() instanceof Player) return;
        if (BetterAC::getInstance()->configManager->reachCheckEnabled) {
            if (!BetterAC::getInstance()->inRange($event->getDamager(), $event->getEntity()->asVector3(), BetterAC::getInstance()->configManager->maxRange[BetterAC::getInstance()->playerClientDataList[$event->getDamager()->getName()]])) {
                BetterAC::getInstance()->warnPlayer($event->getDamager(), PlayerWarnEvent::CAUSE_REACH);
                $event->setCancelled();
                return;
            }
            if (isset(BetterAC::getInstance()->lastMoveUpdates[$event->getDamager()->getName()])) {
                if (!BetterAC::getInstance()->inRange($event->getEntity(), BetterAC::getInstance()->lastMoveUpdates[$event->getDamager()->getName()], BetterAC::getInstance()->configManager->maxRange[BetterAC::getInstance()->playerClientDataList[$event->getDamager()->getName()]])) {
                    BetterAC::getInstance()->warnPlayer($event->getDamager(), PlayerWarnEvent::CAUSE_REACH);
                    $event->setCancelled();
                    return;
                }
            }
        }
        if (BetterAC::getInstance()->configManager->killAuraCheckEnabled) {
            return; // not working yet
            $xDist = $event->getEntity()->x - $event->getDamager()->x;
            $zDist = $event->getEntity()->z - $event->getDamager()->z;
            $yaw = atan2($zDist, $xDist) / M_PI * 180 - 90;
            if($yaw < 0) $yaw += 360.0;
            if (($event->getDamager()->getYaw() - $yaw) < 0) {
                if (($yaw - $event->getDamager()->getYaw()) > $this->maxYawDif[BetterAC::getInstance()->playerClientDataList[$event->getDamager()->getName()]]) {
                }
            } else {
                if (($event->getDamager()->getYaw() - $yaw) > $this->maxYawDif[BetterAC::getInstance()->playerClientDataList[$event->getDamager()->getName()]]) {
                }
            }
        }
        if ($event->getDamager() instanceof Player and BetterAC::getInstance()->configManager->autoClickerCheckEnabled) if (!BetterAC::getInstance()->checkClickRate($event->getDamager())) $event->setCancelled();
    }
    public function onInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        if (!BetterAC::getInstance()->configManager->autoClickerCheckEnabled or !BetterAC::getInstance()->configManager->interactAutoClickerEnabled ) return;
        $hits = 1;
        if (isset(BetterAC::getInstance()->playerHits[$player->getName()]) and BetterAC::getInstance()->playerHits[$player->getName()][1] === time()) $hits = 1 + BetterAC::getInstance()->playerHits[$player->getName()][0];
        BetterAC::getInstance()->playerHits[$player->getName()] = [$hits, time()];
        if (BetterAC::getInstance()->reachedTPSLimit()) return;
        if ($event->getItem()->hasEnchantment(Enchantment::EFFICIENCY) and $event->getItem() instanceof Pickaxe) return;
        if (!BetterAC::getInstance()->checkClickRate($event->getPlayer())) $event->setCancelled();
    }
}