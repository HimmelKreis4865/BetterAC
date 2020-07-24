<?php

namespace HimmelKreis4865\BetterAC\Listeners;

use MongoDB\Driver\Server;
use pocketmine\block\Block;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\math\Vector3;

class JoinListener implements Listener
{
    public function onJoin(PlayerJoinEvent $event)
    {
        if ($event->getPlayer()->getLevel()->getBlock($event->getPlayer()->add(0, 1))->getId() !== Block::AIR) $event->getPlayer()->teleport(new Vector3($event->getPlayer()->getFloorX(), ($event->getPlayer()->getLevel()->getHighestBlockAt($event->getPlayer()->getFloorX(), $event->getPlayer()->getFloorZ()) + 1), $event->getPlayer()->getFloorZ()));
    }
}