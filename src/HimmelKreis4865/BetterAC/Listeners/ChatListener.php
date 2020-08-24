<?php

namespace HimmelKreis4865\BetterAC\Listeners;

use HimmelKreis4865\BetterAC\BetterAC;
use HimmelKreis4865\BetterAC\Events\PlayerWarnEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;

class ChatListener implements Listener
{
    public function onChat(PlayerChatEvent $event)
    {
        if ($event->isCancelled()) return;
        if (isset(BetterAC::getInstance()->playerChatTimes[$event->getPlayer()->getName()]) and BetterAC::getInstance()->playerChatTimes[$event->getPlayer()->getName()] > time()) {
            $event->getPlayer()->sendMessage(BetterAC::PREFIX . BetterAC::getInstance()->getLanguageManager()->getLanguage()->translateString("spam_cooldown", ["{seconds}"], [BetterAC::getInstance()->configManager->spam_cooldown]));
            BetterAC::getInstance()->warnPlayer($event->getPlayer(), PlayerWarnEvent::CAUSE_SPAM);
            $event->setCancelled();
            return;
        }
        BetterAC::getInstance()->playerChatTimes[$event->getPlayer()->getName()] = time() + BetterAC::getInstance()->configManager->spam_cooldown;
    }
}