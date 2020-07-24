<?php

namespace HimmelKreis4865\BetterAC\Providers;

use HimmelKreis4865\BetterAC\BetterAC;

class MySQLProvider extends Provider
{

    private $mysql;

    public function __construct()
    {
        $this->mysql = mysqli_connect(BetterAC::getInstance()->configManager->mysqlSettings["host"], BetterAC::getInstance()->configManager->mysqlSettings["username"], BetterAC::getInstance()->configManager->mysqlSettings["password"], BetterAC::getInstance()->configManager->mysqlSettings["database"]);
        if ($this->mysql->connect_error) BetterAC::getInstance()->getLogger()->emergency("MYSQL CONNECT ERROR: " . $this->mysql->connect_error);
        $this->mysql->query("CREATE TABLE IF NOT EXISTS BetterAC ( `username` TEXT NOT NULL , `warns` INT NOT NULL ) ENGINE = InnoDB;");
    }

    /**
     * @param string $player
     * @return bool
     */
    public function addWarn(string $player): bool
    {
        $warns = $this->getWarns($player) + 1;
        if (!$this->hasAccount($player)) $this->createAccount($player);
        $stmt = $this->mysql->prepare("UPDATE BetterAC SET warns = ? WHERE username = ?");
        $stmt->bind_param("is", $warns, $player);
        return $stmt->execute();
    }

    /**
     * @param string $player
     * @return bool
     */
    public function removeWarn(string $player): bool
    {
        if (!$this->hasAccount($player)) $this->createAccount($player);
        $warns = $this->getWarns($player);
        if ($warns === 0) return false;
        $warns--;
        $stmt = $this->mysql->prepare("UPDATE BetterAC SET warns = ? WHERE username = ?");
        $stmt->bind_param("is", $warns, $player);
        return $stmt->execute();
    }

    /**
     * @param string $player
     * @return int
     */
    public function getWarns(string $player): int
    {
        if (!$this->hasAccount($player)) $this->createAccount($player);
        $stmt = $this->mysql->prepare("SELECT warns FROM BetterAC WHERE username = ?");
        $stmt->bind_param("s", $player);
        $result = $stmt->execute();
        if (!$result) return 0;
        $result = $stmt->get_result();
        if (!$result) return 0;
        if ($result->num_rows === 0) return 0;
        if ($val = $result->fetch_array()) {
            if (isset($val[0])) return $val[0];
        }
        return 0;
    }

    /**
     * @param string $player
     * @return bool
     */
    public function resetWarns(string $player): bool
    {
        if (!$this->hasAccount($player)) $this->createAccount($player);
        $stmt = $this->mysql->prepare("UPDATE BetterAC SET warns = 0 WHERE username = ?");
        $stmt->bind_param("s", $player);
        return $stmt->execute();
    }

    public function hasAccount(string $player) :bool
    {
        $stmt = $this->mysql->prepare("SELECT warns FROM BetterAC WHERE username = ?");
        $stmt->bind_param("s", $player);
        $result = $stmt->execute();
        if (!$result) return false;
        $result = $stmt->get_result();
        if (!$result) return false;
        if ($result->num_rows === 0) return false;
        return true;
    }
    public function createAccount(string $player)
    {
        $stmt = $this->mysql->prepare("INSERT INTO BetterAC (username, warns) VALUES (?, 0)");
        $stmt->bind_param("s", $player);
        $stmt->execute();
    }
}