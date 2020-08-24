<?php

namespace HimmelKreis4865\BetterAC;

use HimmelKreis4865\BetterAC\Events\PlayerWarnEvent;
use HimmelKreis4865\BetterAC\Listeners\BlockListener;
use HimmelKreis4865\BetterAC\Listeners\ChatListener;
use HimmelKreis4865\BetterAC\Listeners\HitListeners;
use HimmelKreis4865\BetterAC\Listeners\JoinListener;
use HimmelKreis4865\BetterAC\Listeners\MoveListener;
use HimmelKreis4865\BetterAC\Listeners\QuitListener;
use HimmelKreis4865\BetterAC\Listeners\XRayListener;
use HimmelKreis4865\BetterAC\Providers\MySQLProvider;
use HimmelKreis4865\BetterAC\Providers\Provider;
use HimmelKreis4865\BetterAC\Providers\YAMLProvider;
use HimmelKreis4865\BetterAC\Tasks\MySQLRefreshTask;
use HimmelKreis4865\BetterAC\utils\ConfigSettingsManager;
use HimmelKreis4865\BetterAC\utils\LanguageManager;
use HimmelKreis4865\BetterAC\utils\Language;
use HimmelKreis4865\BetterAC\utils\UpdateChecker;
use HimmelKreis4865\BetterAC\Listeners\PacketListener;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Pickaxe;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;

class BetterAC extends PluginBase
{
    /** @var null|LanguageManager $languageManager */
    public $languageManager;

    /** @var null | BetterAC */
    private static $instance = null;

    /** @var null | Provider */
    private static $provider = null;

    /** @var null | ConfigSettingsManager */
    public $configManager = null;

    // --- CLIENT DATA TYPES START ----

    const TYPE_MOBILE = 0;

    const TYPE_PC = 1;

    const TYPE_CONSOLE = 2;

    const PREFIX = "§8[§eBetterAC§8] §7";

    // --- CLIENT DATA TYPES END ----

    // ---- PLAYER LIST ARRAYS START ----

    public $playerHits = [];

    /**
     * Saves last move update of players in array (username => Vector3)
     * @var Vector3[]
     */
    public $lastMoveUpdates = [];

    /** @var array  */
    public $playerClientDataList = [];

    /** @var array */
    public $playerChatTimes = [];

    /** @var int[] */
    public $blockBreakTimer = [];

    // ---- PLAYER LIST ARRAYS END ----

    public function onEnable()
    {
        self::$instance = $this;
        Server::getInstance()->getAsyncPool()->submitTask(new UpdateChecker($this->getDescription()->getVersion()));
        $this->saveResource("config.yml");
        $this->configManager = new ConfigSettingsManager();
        $this->initProvider();
        $this->initLanguage();
        if ($this->configManager->spamCheckEnabled)  $this->getServer()->getPluginManager()->registerEvents(new ChatListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new HitListeners(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new PacketListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new MoveListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new JoinListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new BlockListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new QuitListener(), $this);
        if ($this->configManager->xRayCheckEnabled)  {
            $this->getServer()->getPluginManager()->registerEvents(new XRayListener(), $this);
            $this->getLogger()->notice("Development version of Anti - Xray was enabled successfully");
        }
        // task is submitted every 6 hours
        // If you changed mysqli timeout under this 6 hours, please change the code or just edit mysqli settings
        $this->getScheduler()->scheduleRepeatingTask(new MySQLRefreshTask(), (20 * 60 * 60 * 6));

    }

    /**
     * @todo Make this system really working lol :o Maybe it's already working but I dunno yet
      *
     * @return mixed
     */
    public function getLanguageManager() :?LanguageManager
    {
        return $this->languageManager;
    }

    /**
     * Returns an instance of @link BetterAC if its called after onEnable() in PluginBase
     * If you need this function in onEnable() of your own plugin, please make sure to load this plugin before your own
     *
     * @api
     *
     * @return null|BetterAC
     */
    public static function getInstance() :?BetterAC
    {
        return self::$instance;
    }

    /**
     * Returns an instance of the provider
     *
     * @return Provider|null
     */
    public static function getProvider() :?Provider
    {
        return self::$provider;
    }

    public function initLanguage()
    {

        if (!is_dir($this->getDataFolder() . "languages/")) @mkdir($this->getDataFolder() . "languages/");
        $language = $this->getConfig()->get("language");
        if (file_exists($this->getDataFolder() . "languages/" . $language . ".yml")) {
            $file = new Config($this->getDataFolder() . "languages/" . $language . ".yml", Config::YAML);
            $this->languageManager = new LanguageManager(new Language($language, $file->getAll()));
        }
        if (!file_exists($this->getDataFolder() . "languages/eng.yml")) {
            $this->saveResource("eng.yml");
            $f = fopen($this->getDataFolder() . "languages/eng.yml", "w");
            copy($this->getDataFolder() . "eng.yml", $this->getDataFolder() . "languages/eng.yml");
            fclose($f);
        }
        $file = new Config($this->getDataFolder() . "languages/eng.yml", Config::YAML);
        $this->languageManager = new LanguageManager(new Language($language, $file->getAll()));
    }

    public function initProvider()
    {
        $provider = $this->configManager->provider;
        switch (strtolower($provider)) {
            case "mysql":
            case "sql":
                self::$provider = new MySQLProvider();
                break;

                // includes yaml so there's no need to call as case
            default:
                self::$provider = new YAMLProvider();
                break;
        }
    }

    /**
     * Checks if player clickRate is legit or not
     *
     * @api
     *
     * @param Player $player
     *
     * @param bool $pickAxeCheck temporary unused
     *
     * @return true if legit
     * @return false if not legit and player get warned
     */
    public function checkClickRate(Player $player, bool $pickAxeCheck = false) :bool
    {
        $maxClicks = $this->configManager->maxClicksPerSecond[$this->playerClientDataList[$player->getName()]];
        $hits = 1;
        if (isset($this->playerHits[$player->getName()]) and $this->playerHits[$player->getName()][1] === time()) $hits = 1 + $this->playerHits[$player->getName()][0];
        $this->playerHits[$player->getName()] = [$hits, time()];
        if ($hits <= $maxClicks) return true;
        unset($this->playerHits[$player->getName()]);
        $this->warnPlayer($player, PlayerWarnEvent::CAUSE_AUTOCLICKER);
        return false;
    }

    public function inRange(Vector3 $search, Vector3 $target, int $maxRange) :bool
    {
        if ((int)$search->distance($target) < $maxRange) return true;
        return false;
    }

    public function reachedTPSLimit() :bool
    {
        return ($this->getServer()->getTicksPerSecond() <= $this->configManager->minTPS);
    }

    public function warnPlayer(Player $player, int $cause = PlayerWarnEvent::CAUSE_CUSTOM)
    {
        if (!in_array(PlayerWarnEvent::getCauseString($cause), $this->configManager->warned_modules)) return;
        $event = new PlayerWarnEvent($player, $cause);
        $event->call();
        if ($event->isCancelled()) return;
        BetterAC::getProvider()->addWarn($player->getName());
        $this->getLogger()->notice("Player" . $player->getName() . " was automatically warned by BetterAC for [" . PlayerWarnEvent::getCauseString($cause) . "]");
        if ((int) BetterAC::getProvider()->getWarns($player->getName()) > $this->configManager->maxWarnsForBan) {
            foreach ($this->configManager->punishCommands as $command) {
                Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), str_replace(["{playername}", "{reason}"], [$player->getName(), PlayerWarnEvent::getCauseString($cause)], $command));
            }
            $this->getLogger()->notice("Player " . $player->getName() . " was automatically punished by BetterAC for: [" . PlayerWarnEvent::getCauseString($cause) . "]");
            BetterAC::getProvider()->resetWarns($player->getName());
        }
    }
}