<?php

namespace HimmelKreis4865\BetterAC\Providers;

abstract class Provider
{
    /**
     * @param string $player
     *
     * @return bool
     */
    abstract public function addWarn(string $player) :bool;

    /**
     * @param string $player
     *
     * @return bool
     */
    abstract public function removeWarn(string $player) :bool;

    /**
     * @param string $player
     *
     * @return int
     */
    abstract public function getWarns(string $player) :int;

    /**
     * @param string $player
     *
     * @return bool
     */
    abstract public function resetWarns(string $player) :bool;
}