<?php

namespace HimmelKreis4865\BetterAC\Listeners;

use HimmelKreis4865\BetterAC\BetterAC;
use HimmelKreis4865\BetterAC\Events\PlayerWarnEvent;
use pocketmine\entity\Effect;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Player;

class BlockListener implements Listener
{
    public function onPlace(BlockPlaceEvent $event)
    {
        if (!BetterAC::getInstance()->configManager->killAuraCheckEnabled) return;
        if (BetterAC::getInstance()->reachedTPSLimit()) return;
        return; //development version - shouldn't be used on public server
        /*if ($this->minPitch[BetterAC::getInstance()->playerClientDataList[$event->getPlayer()->getName()]] > floor($event->getPlayer()->getPitch())) {
            BetterAC::getInstance()->warnPlayer($event->getPlayer(), PlayerWarnEvent::CAUSE_KILLAURA);
        }*/
    }
    private $minPitch = [
        BetterAC::TYPE_MOBILE => -8,
        BetterAC::TYPE_PC => 40,
        BetterAC::TYPE_CONSOLE => 40
    ];

    public function onInteract(PlayerInteractEvent $event)
    {
        if ($event->isCancelled()) return;
        if (!BetterAC::getInstance()->configManager->instaBreakCheckEnabled) return;
        if($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK) BetterAC::getInstance()->blockBreakTimer[$event->getPlayer()->getRawUniqueId()] = floor(microtime(true) * 20);
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event)
    {
        if ($event->isCancelled()) return;

        if (!BetterAC::getInstance()->configManager->instaBreakCheckEnabled) return;

        $player = $event->getPlayer();
        if ($player->getGamemode() === Player::CREATIVE) return;

        if(!isset(BetterAC::getInstance()->blockBreakTimer[$player->getRawUniqueId()])){
            $event->setCancelled();
            BetterAC::getInstance()->getLogger()->debug("Player " . $player->getName() . " wanted to break a block without starting it, so he got warned.");
            BetterAC::getInstance()->warnPlayer($event->getPlayer(), PlayerWarnEvent::CAUSE_INSTA_BREAK);
            return;
        }
        $expectedTime = ceil($event->getBlock()->getBreakTime($event->getItem()) * 20);
        if($event->getPlayer()->hasEffect(Effect::HASTE)) $expectedTime *= 1 - (0.2 * $player->getEffect(Effect::HASTE)->getEffectLevel());

        if($event->getPlayer()->hasEffect(Effect::MINING_FATIGUE)) $expectedTime *= 1 + (0.3 * $player->getEffect(Effect::MINING_FATIGUE)->getEffectLevel());

        $expectedTime -= 1;
        $actualTime = ceil(microtime(true) * 20) - BetterAC::getInstance()->blockBreakTimer[$player->getRawUniqueId()];

        if($actualTime < $expectedTime){
            BetterAC::getInstance()->getLogger()->debug("Player " . $player->getName() . " is breaking blocks in $actualTime ticks, $expectedTime ticks were expected so player was warned.");
            BetterAC::getInstance()->warnPlayer($event->getPlayer(), PlayerWarnEvent::CAUSE_INSTA_BREAK);
            $event->setCancelled();
            unset(BetterAC::getInstance()->blockBreakTimer[$player->getRawUniqueId()]);
            return;
        }

        unset(BetterAC::getInstance()->blockBreakTimer[$player->getRawUniqueId()]);
    }
}