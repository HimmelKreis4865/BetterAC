<?php

namespace HimmelKreis4865\BetterAC\Listeners;

use HimmelKreis4865\BetterAC\BetterAC;
use pocketmine\block\Block;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector3;
use pocketmine\Player;

class MoveListener implements Listener
{
    public function onMove(PlayerMoveEvent $event) {
        $id = $event->getPlayer()->getLevel()->getBlock($event->getPlayer())->getId();
        if ($event->getPlayer()->getLevel()->getBlock($event->getPlayer()->add(0, 1))->isSolid() and $id !== Block::SAND and $id !== Block::GRAVEL) {
            $event->setCancelled();
            $event->getPlayer()->teleport(new Vector3($event->getPlayer()->getFloorX(), ($event->getPlayer()->getLevel()->getHighestBlockAt($event->getPlayer()->getFloorX(), $event->getPlayer()->getFloorZ()) + 1), $event->getPlayer()->getFloorZ()));
            return;
        }
        BetterAC::getInstance()->lastMoveUpdates[$event->getPlayer()->getName()] = $event->getTo();
    }

    public function onTeleport(EntityTeleportEvent $event) {
        if (!$event->getEntity() instanceof Player) return;
        BetterAC::getInstance()->lastMoveUpdates[$event->getEntity()->getName()] = $event->getTo();
    }

}
