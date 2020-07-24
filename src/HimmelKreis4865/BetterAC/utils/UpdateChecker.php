<?php

namespace HimmelKreis4865\BetterAC\utils;

use HimmelKreis4865\BetterAC\BetterAC;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;

class UpdateChecker extends AsyncTask
{
    const BASE_URL = "https://raw.githubusercontent.com/HimmelKreis4865/BetterAC/master/plugin.yml";

    private $currentVersion;

    public function __construct(string $currentVersion)
    {
        $this->currentVersion = $currentVersion;
    }

    public function onRun()
    {
        $file = Internet::getURL(self::BASE_URL);
        $file = yaml_parse($file);
        var_dump($file);
        var_dump($this->currentVersion);
        if (isset($file["version"]) and $file["version"] === $this->currentVersion) {
            $this->setResult(true);
        } else {
            $this->setResult(false);
        }
    }
    public function onCompletion(Server $server)
    {
        if (!$this->getResult()) {
            BetterAC::getInstance()->getLogger()->error("You are not using latest plugin version! Please update the plugin to use our best features! Plugin will disable now!");
            BetterAC::getInstance()->setEnabled(false);
        }
    }
}