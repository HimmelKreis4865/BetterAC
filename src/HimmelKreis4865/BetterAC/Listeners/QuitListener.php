<?php

namespace HimmelKreis4865\BetterAC\Listeners;

use HimmelKreis4865\BetterAC\BetterAC;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;

class QuitListener implements Listener
{
    public function onQuit(PlayerQuitEvent $event)
    {
        if (isset(BetterAC::getInstance()->playerChatTimes[$event->getPlayer()->getName()])) unset(BetterAC::getInstance()->playerChatTimes[$event->getPlayer()->getName()]);
        if (isset(BetterAC::getInstance()->playerHits[$event->getPlayer()->getName()])) unset(BetterAC::getInstance()->playerHits[$event->getPlayer()->getName()]);
        if (isset(BetterAC::getInstance()->playerClientDataList[$event->getPlayer()->getName()])) unset(BetterAC::getInstance()->playerClientDataList[$event->getPlayer()->getName()]);
        if (isset(BetterAC::getInstance()->lastMoveUpdates[$event->getPlayer()->getName()])) unset(BetterAC::getInstance()->lastMoveUpdates[$event->getPlayer()->getName()]);
        if (isset(BetterAC::getInstance()->blockBreakTimer[$event->getPlayer()->getRawUniqueId()])) unset(BetterAC::getInstance()->blockBreakTimer[$event->getPlayer()->getRawUniqueId()]);
    }
}