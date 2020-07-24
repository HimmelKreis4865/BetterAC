<?php

namespace HimmelKreis4865\BetterAC\Listeners;

use HimmelKreis4865\BetterAC\BetterAC;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;

class ChatListener implements Listener
{
    public $playerTimes = [];

    public function onChat(PlayerChatEvent $event)
    {
        if ($event->isCancelled()) return;
        if (isset($this->playerTimes[$event->getPlayer()->getName()]) and $this->playerTimes[$event->getPlayer()->getName()] > time()) {
            $event->getPlayer()->sendMessage(BetterAC::PREFIX . BetterAC::getInstance()->getLanguageManager()->getLanguage()->translateString("spam_cooldown", ["{seconds}"], [BetterAC::getInstance()->configManager->spam_cooldown]));
            $event->setCancelled();
            return;
        }
        $this->playerTimes[$event->getPlayer()->getName()] = time() + BetterAC::getInstance()->configManager->spam_cooldown;
    }
}