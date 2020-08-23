<?php

namespace HimmelKreis4865\BetterAC\Providers;

abstract class Provider
{
    /**
     * Add a warn to a player in string format (player's name)
     *
     * @param string $player
     *
     * @return bool
     */
    abstract public function addWarn(string $player) :bool;

    /**
     * Remove a warn from a player (also string format, player's name)
     *
     * @param string $player
     *
     * @return bool
     */
    abstract public function removeWarn(string $player) :bool;

    /**
     * Return a number of warns given to player (also string format, player's name)
     *
     * @param string $player
     *
     * @return int
     */
    abstract public function getWarns(string $player) :int;

    /**
     * Set the warns of player (also string format, player's name) to 0
     *
     * @param string $player
     *
     * @return bool
     */
    abstract public function resetWarns(string $player) :bool;
}