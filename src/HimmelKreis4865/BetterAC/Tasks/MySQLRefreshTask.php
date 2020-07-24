<?php

namespace HimmelKreis4865\BetterAC\Tasks;

use HimmelKreis4865\BetterAC\BetterAC;
use pocketmine\scheduler\Task;

class MySQLRefreshTask extends Task
{
    public function onRun(int $currentTick)
    {
        /* Just to remove mysql timeouts */
        BetterAC::getProvider()->getWarns("testPlayer");
    }
}