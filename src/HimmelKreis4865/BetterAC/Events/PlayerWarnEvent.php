<?php

namespace HimmelKreis4865\BetterAC\Events;

use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerEvent;
use pocketmine\Player;

class PlayerWarnEvent extends PlayerEvent implements Cancellable
{
    private $cause;

    protected $player;

    public const CAUSE_AUTOCLICKER = 0;
    public const CAUSE_KILLAURA = 1;
    public const CAUSE_REACH = 2;
    public const CAUSE_SPEED = 3;
    public const CAUSE_NOCLIP = 4;
    public const CAUSE_CUSTOM = 5;

    public function __construct(Player $player, int $cause = self::CAUSE_CUSTOM)
    {
        $this->player = $player;
        $this->cause = $cause;
    }
    public function getPlayer(): Player
    {
        return $this->player;
    }
    public function getCause() :int
    {
        return $this->cause;
    }

    public static function getCauseString(int $cause) :string
    {
        switch ($cause) {
            case self::CAUSE_AUTOCLICKER: return "Autoclicker";
            case self::CAUSE_KILLAURA: return "Killaura";
            case self::CAUSE_REACH: return "Reach";
            case self::CAUSE_SPEED: return "Speed";
            case self::CAUSE_NOCLIP: return "No-Clip";
            default: return "";
        }
    }
}
