<?php

declare(strict_types=1);

namespace david\itemStacker;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntitySpawnEvent;

use pocketmine\entity\object\ItemEntity;

class EventListener implements Listener {

    public function onEntitySpawn(EntitySpawnEvent $event) : void{
        $entity = $event->getEntity();
        
        if (!$entity instanceof ItemEntity) {
            return;
        }
        
        $position = $entity->getPosition();
        $world = $position->getWorld();
        $entities = $world->getNearbyEntities($entity->getBoundingBox()->expandedCopy(5, 5, 5));
        
        if (empty($entities)) {
            return;
        }
        
        $originalItem = $entity->getItem();
        $totalItemCount = $originalItem->getCount();
        
        foreach ($entities as $e) {
            if ($e instanceof ItemEntity && $entity !== $e) {
                $itemE = $e->getItem();
                if ($itemE->equals($originalItem)) {
                    $e->flagForDespawn();
                    $originalItem->setCount($originalItem->getCount() + $itemE->getCount());
                    $totalItemCount += $itemE->getCount();
                }
            }
        }
        
        if ($totalItemCount > 1) {
            $itemName = $originalItem->getName();
            $tag = "§7" . $itemName . " §7x§b" . $totalItemCount;
            $entity->setNameTag($tag);
            $entity->setNameTagAlwaysVisible(true);
        }
    }
}
