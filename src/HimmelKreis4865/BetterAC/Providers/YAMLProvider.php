<?php

namespace HimmelKreis4865\BetterAC\Providers;

use HimmelKreis4865\BetterAC\BetterAC;
use pocketmine\block\Concrete;
use pocketmine\utils\Config;

class YAMLProvider extends Provider
{
    public function __construct()
    {
        if (!file_exists(BetterAC::getInstance()->getDataFolder() . "warns.yml")) BetterAC::getInstance()->saveResource("players.yml");
    }

    /**
     * @param string $player
     *
     * @return bool
     */
    public function addWarn(string $player): bool
    {
        $config = new Config(BetterAC::getInstance()->getDataFolder() . "warns.yml", Config::YAML);
        if ($config->exists($player)) {
            $config->set($player, ((int) $config->get($player) + 1));
        } else {
            $config->set($player, 1);
        }
        $config->save();
        return true;
    }

    /**
     * @param string $player
     *
     * @return bool
     */
    public function removeWarn(string $player): bool
    {
        $config = new Config(BetterAC::getInstance()->getDataFolder() . "warns.yml", Config::YAML);
        if (!$config->exists($player) or (int)$config->get($player) < 1) return false;
        $config->set($player, ((int)$config->get($player) - 1));
        $config->save();
        return true;
    }

    /**
     * @param string $player
     *
     * @return bool
     */
    public function resetWarns(string $player): bool
    {
        $config = new Config(BetterAC::getInstance()->getDataFolder() . "warns.yml", Config::YAML);
        if (!$config->exists($player)) return false;
        $config->set($player, 0);
        $config->save();
        return true;
    }

    /**
     * @param string $player
     *
     * @return int
     */
    public function getWarns(string $player): int
    {
        $config = new Config(BetterAC::getInstance()->getDataFolder() . "warns.yml", Config::YAML);
        if (!$config->exists($player) or !is_numeric($config->get($player))) return 0;
        return $config->get($player);
    }
}