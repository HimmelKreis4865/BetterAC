<?php

namespace HimmelKreis4865\BetterAC\Events;

use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerEvent;
use pocketmine\Player;

class PlayerWarnEvent extends PlayerEvent implements Cancellable
{
    protected $player;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }
    public function getPlayer(): Player
    {
        return $this->player;
    }
}