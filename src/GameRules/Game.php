<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules;

use Htw\GameRules\Events\ArrowHitPlayer;
use Htw\GameRules\Events\ArrowMissed;
use Htw\GameRules\Events\ArrowRandomFlight;
use Htw\GameRules\Events\FellInPit;
use Htw\GameRules\Events\InvalidMove;
use Htw\GameRules\Events\LeadRoomBat;
use Htw\GameRules\Events\LeadRoomPit;
use Htw\GameRules\Events\LeadRoomWumpus;
use Htw\GameRules\Events\OutOfArrows;
use Htw\GameRules\Events\PlayerGotWumpus;
use Htw\GameRules\Events\SuperBatSnatch;
use Htw\GameRules\Events\WumpusGotYou;
use Htw\GameRules\Events\WumpusWakedUp;
use Htw\GameRules\Events\YouAreInRoom;
use Htw\GameRules\WorldObjects\Bat;
use Htw\GameRules\WorldObjects\Pit;
use Htw\GameRules\WorldObjects\Wumpus;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

final class Game implements EventDispatcherInterface, ListenerProviderInterface
{
    private World $world;
    private array $listeners = [];

    public function __construct(
        World $world
    ) {
        $this->world = $world;
    }

    public function sensePlayerRoom(int $playerId): void
    {
        $playerRoom = $this->world->getPlayerRoom($playerId);
        $leadRooms = $this->world->getLeadRooms($playerRoom);

        foreach ($leadRooms as $leadRoom) {
            $leadRoomHazard = $this->world->getRoomObject($leadRoom);
            if ($leadRoomHazard) {
                switch ($leadRoomHazard->getType()) {
                    case WorldObjectInterface::TYPE_PIT:
                        $this->dispatch(new LeadRoomPit($playerId));
                        break;

                    case WorldObjectInterface::TYPE_BAT:
                        $this->dispatch(new LeadRoomBat($playerId));
                        break;

                    case WorldObjectInterface::TYPE_WUMPUS:
                        $this->dispatch(new LeadRoomWumpus($playerId));
                        break;
                }
            }
        }

        $this->dispatch(new YouAreInRoom($playerId, $playerRoom, $leadRooms));
    }

    public function move(int $playerId, int $whereTo): void
    {
        $playerRoom = $this->world->getPlayerRoom($playerId);
        $leadRooms = $this->world->getLeadRooms($playerRoom);

        if (!in_array($whereTo, $leadRooms)) {
            $this->dispatch(new InvalidMove($playerId, $whereTo));
            return;
        }

        check_room:

        $roomHazard = $this->world->getRoomObject($whereTo);

        if (empty($roomHazard)) {
            $this->world->moveRoomObject($playerRoom, $whereTo);
            return;
        }

        if ($roomHazard instanceof Wumpus) {
            $this->world->getPlayer($playerId)->die();
            $this->dispatch(new WumpusGotYou($playerId));
        }

        if ($roomHazard instanceof Pit) {
            $this->world->getPlayer($playerId)->die();
            $this->dispatch(new FellInPit($playerId));
        }

        if ($roomHazard instanceof Bat) {
            $whereTo = $this->world->getRandomFreeRoom();
            $this->world->moveRoomObject($playerRoom, $whereTo);
            $this->dispatch(new SuperBatSnatch($playerId, $playerRoom, $whereTo));

            goto check_room;
        }
    }

    public function shoot(int $playerId, array $arrowTrajectory): void
    {
        $playerRoom = $this->world->getPlayerRoom($playerId);

        $player = $this->world->getPlayer($playerId);

        $player->decreaseArrow();

        $prevRoom = $playerRoom;

        /* Arrow trajectory */

        $actualArrowFlight = [];
        $randomArrowFlight = false;
        foreach ($arrowTrajectory as $arrowRoom) {
            if (!$this->world->existTunnel($prevRoom, $arrowRoom)) {
                $randomArrowFlight = true;
            }

            $actualArrowRoom = $randomArrowFlight
                ? $this->world->getRandomLeadRoom($prevRoom)
                : $arrowRoom;

            $prevRoom = $actualArrowRoom;

            $actualArrowFlight[] = $actualArrowRoom;
        }

        if ($randomArrowFlight) {
            $this->dispatch(new ArrowRandomFlight($playerId));
        }

        /* Arrow flight */

        $wakedUpWumpusRooms = [];

        foreach ($actualArrowFlight as $arrowRoom) {
            $roomObject = $this->world->getRoomObject($arrowRoom);

            foreach ($this->world->getLeadRooms($arrowRoom) as $arrowLeadRoom) {
                if (
                    empty($newWumpusRoom)
                    && $this->world->roomHasObject(WorldObjectInterface::TYPE_WUMPUS, $arrowLeadRoom)
                    && rand(1, 100) <= 75 // P = 0.75
                ) {
                    $wakedUpWumpusRooms[] = $arrowLeadRoom;
                }
            }

            if (empty($roomObject)) {
                continue;
            }

            if ($roomObject instanceof ArrowHittableWorldObjectInterface) {
                $roomObject->hit();

                if ($roomObject->getType() === WorldObjectInterface::TYPE_WUMPUS) {
                    $player->gotWumpus();
                    $this->world->cleanRoom($arrowRoom);
                    $this->dispatch(new PlayerGotWumpus($playerId));
                }

                if ($roomObject->getType() === WorldObjectInterface::TYPE_PLAYER) {
                    $this->dispatch(new ArrowHitPlayer($playerId));
                }

                return;
            }
        }

        if (!$this->world->getPlayer($playerId)->hasArrow()) {
            $this->dispatch(new OutOfArrows($playerId));
            return;
        }

        $this->dispatch(new ArrowMissed($playerId));

        foreach ($wakedUpWumpusRooms as $wakedUpRoom) {
            $this->world->moveRoomObject(
                $wakedUpRoom,
                $this->world->getRandomLeadRoom($wakedUpRoom)
            );

            $this->dispatch(new WumpusWakedUp($playerId));

            if ($playerRoom === $wakedUpRoom) {
                $this->world->getPlayer($playerId)->die();
                $this->dispatch(new WumpusGotYou($playerId));
            }
        }
    }

    public function existRoom(int $room): bool
    {
        return $this->world->existRoom($room);
    }

    public function getNumRooms(): int
    {
        return $this->world->getNumRooms();
    }

    public function isPlayerGameOver(string $playerId): bool
    {
        return $this->world->getPlayer($playerId)->isGameOver();
    }

    public function isPlayerGotWumpus(string $playerId): bool
    {
        return $this->world->getPlayer($playerId)->isGotWumpus();
    }

    /**
     * @param object|GameEventInterface $event
     */
    public function dispatch(object $event): void
    {
        foreach ($this->getListenersForEvent($event) as $listener) {
            $listener($event);
        }
    }

    /**
     * @param callable $listener
     * @throws \ReflectionException
     */
    public function addEventListener(callable $listener): void
    {
        $closure = new \ReflectionFunction(\Closure::fromCallable($listener));
        $params = $closure->getParameters();

        /** @var \ReflectionNamedType $reflectedType */
        $reflectedType = isset($params[0]) ? $params[0]->getType() : null;

        if ($reflectedType === null) {
            throw new \InvalidArgumentException('Listeners must declare an object type they can accept.');
        }

        $this->listeners[$reflectedType->getName()][] = $listener;
    }

    /**
     * @param object $event
     * @return iterable
     */
    public function getListenersForEvent(object $event): iterable
    {
        foreach (array_keys($this->listeners) as $eventClass) {
            if ($event instanceof $eventClass) {
                yield from $this->listeners[$eventClass];
            }
        }
    }
}
