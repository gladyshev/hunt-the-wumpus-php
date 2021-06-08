<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules;

use Htw\GameRules\Events\FellInPit;
use Htw\GameRules\Events\InvalidRoom;
use Htw\GameRules\Events\LeadRoomHazard;
use Htw\GameRules\Events\SuperBatSnatch;
use Htw\GameRules\Events\WumpusGotYou;
use Htw\GameRules\Events\YouAreInRoom;
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
                $this->dispatch(new LeadRoomHazard($leadRoomHazard));
            }
        }

        $this->dispatch(new YouAreInRoom($playerRoom, $leadRooms));
    }

    public function move(int $playerId, int $whereTo): void
    {

        $playerRoom = $this->world->getPlayerRoom($playerId);
        $leadRooms = $this->world->getLeadRooms($playerRoom);

        if (!in_array($whereTo, $leadRooms)) {
            $this->dispatch(new InvalidRoom($playerId, $whereTo));
            return;
        }

        $roomHazard = $this->world->getRoomObject($whereTo);

        if (empty($roomHazard)) {
            $this->world->moveRoomObject($playerRoom, $whereTo);
            return;
        }

        switch ($roomHazard->getType()) {
            case Hazard::TYPE_WUMPUS:
                $this->world->diePlayer($playerId);
                $this->dispatch(new WumpusGotYou($playerId));
                break;

            case Hazard::TYPE_PIT:
                $this->world->diePlayer($playerId);
                $this->dispatch(new FellInPit($playerId));
                break;

            case Hazard::TYPE_BAT:
                $newPlayerRoom = $this->world->getRandomFreeRoom();
                $this->world->moveRoomObject($playerRoom, $newPlayerRoom);
                $this->dispatch(new SuperBatSnatch($playerId, $playerRoom, $newPlayerRoom));
                break;
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
