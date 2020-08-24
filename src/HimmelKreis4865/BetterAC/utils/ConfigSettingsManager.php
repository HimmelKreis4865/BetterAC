<?php

namespace HimmelKreis4865\BetterAC\utils;

use HimmelKreis4865\BetterAC\BetterAC;
use pocketmine\Server;

class ConfigSettingsManager
{
    public $ignoreOp = false;
    public $minTPS = 15;
    public $maxWarnsForBan = 4;
    public $punishCommands = ["ban {playername} hacking", "say {playername} got punished for hacking!"];

    public $provider = "yaml";
    public $mysqlSettings = [
        "host" => "127.0.0.1",
        "username" => "user",
        "password" => "password",
        "database" => "db"
    ];

    public $autoClickerCheckEnabled = false;
    public $interactAutoClickerEnabled = false;
    public $maxClicksPerSecond = [
        BetterAC::TYPE_PC => 35,
        BetterAC::TYPE_MOBILE => 25,
        BetterAC::TYPE_CONSOLE => 15
    ];

    public $reachCheckEnabled = false;
    public $maxRange = [
        BetterAC::TYPE_PC => 6,
        BetterAC::TYPE_CONSOLE => 6,
        BetterAC::TYPE_MOBILE => 8
    ];

    public $speedCheckEnabled = false;
    public $noClipEnabled = false;

    public $spamCheckEnabled = false;
    public $spam_cooldown = 3;

    public $instaBreakCheckEnabled = false;
    public $killAuraCheckEnabled = true;

    public $xRayCheckEnabled = false;
    public $xRayLevels = [];

    public $warned_modules = [];

    public function __construct()
    {
        $file = BetterAC::getInstance()->getConfig();

        if ($file->exists("anti_autoclicker") and (bool) $file->get("anti_autoclicker")) $this->autoClickerCheckEnabled = true;
        if ($file->exists("anti_spam") and (bool) $file->get("anti_spam")) $this->spamCheckEnabled = true;
        if ($file->exists("anti_noclip") and (bool) $file->get("anti_noclip")) $this->noClipEnabled = true;
        if ($file->exists("anti_speed") and (bool) $file->get("anti_speed")) $this->speedCheckEnabled = true;
        if ($file->exists("anti_reach") and (bool) $file->get("anti_reach")) $this->reachCheckEnabled = true;
        if ($file->exists("anti_killaura") and (bool) $file->get("anti_killaura")) $this->killAuraCheckEnabled = true;
        if ($file->exists("interactAutoClickerEnabled") and (bool) $file->get("interactAutoClickerEnabled")) $this->interactAutoClickerEnabled = true;
        if ($file->exists("anti_instabreak") and (bool) $file->get("anti_instabreak")) $this->instaBreakCheckEnabled = true;
        if ($file->exists("anti_xray") and (bool) $file->get("anti_xray") and $file->exists("xray_levels") and isset($file->get("xray_levels")[0])) {
            $this->xRayCheckEnabled = true;
            $this->xRayLevels = $file->get("xray_levels");
        }

        if ($file->exists("provider") and $file->get("provider") === "mysql") {
            if ($file->getNested("mysql.host") !== null and $file->getNested("mysql.username") !== null and $file->getNested("mysql.password") !== null and $file->getNested("mysql.database") !== null) {
                $this->mysqlSettings = [
                    "host" => $file->getNested("mysql.host"),
                    "username" => $file->getNested("mysql.username"),
                    "password" => $file->getNested("mysql.password"),
                    "database" => $file->getNested("mysql.database")
                ];
                $this->provider = "mysql";
            }

        }
        if ($file->exists("check_ops") and (bool) $file->get("check_ops")) $this->ignoreOp = true;
        if ($file->exists("punish_commands") and is_array($file->get("punish_commands"))) $this->punishCommands = $file->get("punish_commands");
        if ($file->exists("min_tps")) $this->minTPS = $file->get("min_tps");
        if ($file->exists("max_warns") and is_numeric($file->get("max_warns"))) $this->maxWarnsForBan = (int) $file->get("max_warns");
        if ($file->exists("autoclicker_pc") and $file->exists("autoclicker_console") and $file->exists("autoclicker_mobile")) {
            $this->maxClicksPerSecond = [
                BetterAC::TYPE_MOBILE => (int) $file->get("autoclicker_mobile"),
                BetterAC::TYPE_PC => (int) $file->get("autoclicker_pc"),
                BetterAC::TYPE_CONSOLE => (int) $file->get("autoclicker_console")
            ];
        }
        if ($file->exists("range_pc") and $file->exists("range_console") and $file->exists("range_mobile")) {
            $this->maxRange = [
                BetterAC::TYPE_MOBILE => (int) $file->get("range_mobile"),
                BetterAC::TYPE_PC => (int) $file->get("range_pc"),
                BetterAC::TYPE_CONSOLE => (int) $file->get("range_console")
            ];
        }
        if ($file->exists("spam_cooldown") and is_numeric($file->get("spam_cooldown"))) $this->spam_cooldown = (int) $file->get("spam_cooldown");

        if ($file->exists("warned_modules") and is_array($file->get("warned_modules"))) $this->warned_modules = $file->get("warned_modules");
    }
}