<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules;

use Htw\GameRules\Events\ArrowHit;
use Htw\GameRules\Events\ArrowMissed;
use Htw\GameRules\Events\ArrowRandomFlight;
use Htw\GameRules\Events\FellInPit;
use Htw\GameRules\Events\InvalidMove;
use Htw\GameRules\Events\LeadRoomHazard;
use Htw\GameRules\Events\OutOfArrows;
use Htw\GameRules\Events\SuperBatSnatch;
use Htw\GameRules\Events\WumpusGotYou;
use Htw\GameRules\Events\WumpusWakedUp;
use Htw\GameRules\Events\YouAreInRoom;
use Htw\GameRules\WorldObjects\Pit;
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
                $this->dispatch(new LeadRoomHazard($playerId, $leadRoomHazard));
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

        $roomHazard = $this->world->getRoomObject($whereTo);

        if (empty($roomHazard)) {
            $this->world->moveRoomObject($playerRoom, $whereTo);
            return;
        }

        switch ($roomHazard->getType()) {
            case Pit::TYPE_WUMPUS:
                $this->world->getPlayer($playerId)->die();
                $this->dispatch(new WumpusGotYou($playerId));
                break;

            case Pit::TYPE_PIT:
                $this->world->getPlayer($playerId)->die();
                $this->dispatch(new FellInPit($playerId));
                break;

            case Pit::TYPE_BAT:
                $newPlayerRoom = $this->world->getRandomFreeRoom();
                $this->world->moveRoomObject($playerRoom, $newPlayerRoom);
                $this->dispatch(new SuperBatSnatch($playerId, $playerRoom, $newPlayerRoom));
                break;
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
                : $arrowRoom
            ;

            $prevRoom = $actualArrowRoom;

            $actualArrowFlight[] = $actualArrowRoom;
        }

        if ($randomArrowFlight) {
            $this->dispatch(new ArrowRandomFlight($playerId));
        }

        /* Arrow flight */

        $newWumpusRoom = null;

        foreach ($actualArrowFlight as $arrowRoom)
        {
            $roomObject = $this->world->getRoomObject($arrowRoom);

            foreach ($this->world->getLeadRooms($arrowRoom) as $arrowLeadRoom) {
                if (
                    $this->world->roomHasHazard(WorldObjectInterface::TYPE_WUMPUS, $arrowLeadRoom)
                    && true // TODO: 75%
                ) {
                    $newWumpusRoom = $arrowLeadRoom;
                }
            }

            if (empty($roomObject)) {
                continue;
            }

            if ($roomObject instanceof DieableWorldObjectInterface) {
                $roomObject->die();
                $this->dispatch(new ArrowHit($playerId, $roomObject));
                if ($roomObject->getType() === WorldObjectInterface::TYPE_WUMPUS) {
                    $player->gotWumpus();
                    $this->world->cleanRoom($arrowRoom);
                }
                return;
            }
        }

        if (!$this->world->getPlayer($playerId)->hasArrow()) {
            $this->dispatch(new OutOfArrows($playerId));
            return;
        }

        $this->dispatch(new ArrowMissed($playerId));

        if ($newWumpusRoom) {
            $this->world->moveRoomObject(
                $newWumpusRoom,
                $this->world->getRandomLeadRoom($newWumpusRoom)
            );
            $this->dispatch(new WumpusWakedUp($playerId));
            if ($playerRoom === $newWumpusRoom) {
                $this->world->getPlayer($playerId)->die();
                $this->dispatch(new WumpusGotYou($playerId));
            }
        }
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
