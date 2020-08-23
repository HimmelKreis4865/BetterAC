<?php

namespace HimmelKreis4865\BetterAC\Listeners;

use HimmelKreis4865\BetterAC\BetterAC;
use HimmelKreis4865\BetterAC\Tasks\ChunkModificationTask;
use HimmelKreis4865\BetterAC\utils\ModifiedChunk;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\LevelChunkPacket;
use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\Server;


class XRayListener implements Listener
{
    /**
     * @ignoreCancelled false
     * @param DataPacketSendEvent $event
     */
    public function chunkPacket(DataPacketSendEvent $event)
    {
        if (!in_array($event->getPlayer()->getLevel()->getName(), BetterAC::getInstance()->configManager->xRayLevels)) return;
        /** @var $batch BatchPacket */
        if (($batch = $event->getPacket()) instanceof BatchPacket && !($batch instanceof ModifiedChunk)) {
            if (isset($this->wl_worlds[$event->getPlayer()->getLevel()->getFolderName()]) || isset($this->wl_players[$event->getPlayer()->getName()]))
                return;

            $batch->decode();

            foreach ($batch->getPackets() as $packet) {
                $chunkPacket = PacketPool::getPacket($packet);
                if ($chunkPacket instanceof LevelChunkPacket) {
                    $chunkPacket->decode();
                    Server::getInstance()->getAsyncPool()->submitTask(new ChunkModificationTask($event->getPlayer()->getLevel()->getChunk($chunkPacket->getChunkX(), $chunkPacket->getChunkZ()), $event->getPlayer()));
                    $event->setCancelled();
                }
            }

        }
    }

    public function updateBlocks(BlockBreakEvent $event)
    {
        var_dump(BetterAC::getInstance()->configManager->xRayLevels);
        if (!in_array($event->getPlayer()->getLevel()->getName(), BetterAC::getInstance()->configManager->xRayLevels)) return;
        $blocks = [$event->getBlock()->asVector3()];
        $players = $event->getBlock()->getLevel()->getChunkPlayers($event->getBlock()->getFloorX() >> 4, $event->getBlock()->getFloorZ() >> 4);

        foreach (ChunkModificationTask::BLOCK_SIDES as $side) {
            $side = $blocks[0]->getSide($side);

            foreach (ChunkModificationTask::BLOCK_SIDES as $side_2)
                $blocks[] = $side->getSide($side_2);

            $blocks[] = $side;
        }

        $event->getPlayer()->getLevel()->sendBlocks($players, $blocks, UpdateBlockPacket::FLAG_NEIGHBORS);
    }

    public function onExplode(EntityExplodeEvent $event)
    {
        // Explosions could spot some ores ppl could destroy so thats not good
        if (in_array($event->getEntity()->getLevel()->getName(), BetterAC::getInstance()->configManager->xRayLevels)) $event->setCancelled();
    }
}