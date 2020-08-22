<?php

namespace HimmelKreis4865\BetterAC\Listeners;

use HimmelKreis4865\BetterAC\BetterAC;
use HimmelKreis4865\BetterAC\Events\PlayerWarnEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\Player;

class BlockListener implements Listener
{
    public function onPlace(BlockPlaceEvent $event)
    {
        if (!BetterAC::getInstance()->configManager->killAuraCheckEnabled) return;
        if (BetterAC::getInstance()->reachedTPSLimit()) return;
        if ($this->minPitch[BetterAC::getInstance()->playerClientDataList[$event->getPlayer()->getName()]] > floor($event->getPlayer()->getPitch())) {
            BetterAC::getInstance()->warnPlayer($event->getPlayer(), PlayerWarnEvent::CAUSE_KILLAURA);
        }
    }
    private $minPitch = [
        BetterAC::TYPE_MOBILE => -8,
        BetterAC::TYPE_PC => 40,
        BetterAC::TYPE_CONSOLE => 40
    ];
}