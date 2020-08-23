<?php

namespace HimmelKreis4865\BetterAC\Listeners;

use HimmelKreis4865\BetterAC\BetterAC;
use pocketmine\block\Block;
use pocketmine\event\level\ChunkLoadEvent;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelChunkPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;

class PacketListener implements Listener
{

    /**
     * @param DataPacketReceiveEvent $event
     */
    public function onDataReceive(DataPacketReceiveEvent $event) {
        $packet = $event->getPacket();
        if ($packet instanceof LoginPacket) {
            $device = $packet->clientData["DeviceOS"];
            if ($device === DeviceOS::ANDROID or $device === DeviceOS::IOS or $device === DeviceOS::WINDOWS_PHONE or $device === DeviceOS::AMAZON or $device === DeviceOS::OSX) {
                BetterAC::getInstance()->playerClientDataList[$packet->username] = BetterAC::TYPE_MOBILE;
            } else if ($device === DeviceOS::WINDOWS_10 or $device === DeviceOS::WIN32) {
                BetterAC::getInstance()->playerClientDataList[$packet->username] = BetterAC::TYPE_PC;
            } else if ($device === DeviceOS::XBOX or $device === DeviceOS::NINTENDO or $device === DeviceOS::PLAYSTATION or $device === DeviceOS::TVOS) {
                BetterAC::getInstance()->playerClientDataList[$packet->username] = BetterAC::TYPE_CONSOLE;
            } else {
                $event->getPlayer()->close(BetterAC::getInstance()->getLanguageManager()->getLanguage()->translateString("kick_unknown_os"));
                $event->setCancelled();
            }
        }
    }

}