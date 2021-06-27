<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\Cli;

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
use Htw\GameRules\WorldFactory;
use Htw\GameRules\HazardFactory;
use Htw\GameRules\WorldObjectInterface;
use Htw\IO\IOInterface;
use Htw\GameRules\WorldObjects\Pit;
use Htw\GameRules\WorldObjects\Player;
use Htw\Config\ConfigInterface;
use Htw\GameRules\Game;

final class Main
{
    private ConfigInterface $config;
    private IOInterface $io;
    private HazardFactory $worldObjectFactory;
    private WorldFactory $worldFactory;

    public function __construct(
        IOInterface $io,
        ConfigInterface $config,
        HazardFactory $worldObjectFactory,
        WorldFactory $worldFactory
    ) {
        $this->io = $io;
        $this->config = $config;
        $this->worldObjectFactory = $worldObjectFactory;
        $this->worldFactory = $worldFactory;
    }

    /**
     * @param int $playerId
     * @param string $playerName
     * @throws \ReflectionException
     */
    public function start(
        int $playerId = 1,
        string $playerName = 'player1'
    ): void {
        $this->io->println('welcome');
        $this->io->println('disclaimer');

        main_menu:

        $command = strtoupper(trim($this->io->input('cli.welcome')));

        if ($command === 'I') {
            $this->io->println('instructions');
            goto main_menu;
        }

        if ($command === 'Q') {
            $this->io->println('exit');
            return;
        }

        $this->io->println('start-game');
        $this->io->println();


        $map = $this->config->getParam('map');

        /* Init game and objects */

        $player = new Player(
            $this->config->getParam('arrows'),
            $playerName,
            $playerId
        );

        $worldObjects = [];

        $worldObjects[] = $player;

        foreach ($this->config->getParam('hazards') as $hazardType) {
            $worldObjects[] = $this->worldObjectFactory->createByType(
                $hazardType
            );
        }

        $world = $this->worldFactory->create($map, $worldObjects);

        $game = new Game($world);

        $game->addEventListener(function (InvalidMove $event): void {
            $this->io->println('invalid-move-room');
        });

        $game->addEventListener(function (LeadRoomHazard $event): void {
            if ($event->getHazard()->getType() === Pit::TYPE_PIT) {
                $this->io->println('feel-draft');
            }

            if ($event->getHazard()->getType() === Pit::TYPE_BAT) {
                $this->io->println('feel-bats');
            }

            if ($event->getHazard()->getType() === Pit::TYPE_WUMPUS) {
                $this->io->println('feel-wumpus');
            }
        });

        $game->addEventListener(function (YouAreInRoom $event): void {
            $this->io->println("you-room", [
                'room' => $event->getRoom()
            ]);

            $this->io->println('you-room-tunnels', [
                'room1' => $event->getLeadRooms()[0],
                'room2' => $event->getLeadRooms()[1],
                'room3' => $event->getLeadRooms()[2],
            ]);
        });

        $game->addEventListener(function (SuperBatSnatch $event): void {
            $this->io->println('super-bat-snatch');
        });

        $game->addEventListener(function (WumpusGotYou $event): void {
            $this->io->println("wumpus-got-you");
        });
        
        $game->addEventListener(function (OutOfArrows $event): void {
            $this->io->println("OUT OF ARROWS.");
        });

        $game->addEventListener(function (FellInPit $event): void {
            $this->io->println("fell-in-pit");
        });

        $game->addEventListener(function (ArrowHit $arrowHit): void {
            $type = $arrowHit->getWorldObject()->getType();
            switch ($type) {
                case WorldObjectInterface::TYPE_WUMPUS:
                    $this->io->println('you-got-wumpus');
                    break;

                case WorldObjectInterface::TYPE_PLAYER:
                    $this->io->println('arrow-got-you');
            }
        });

        $game->addEventListener(function (WumpusWakedUp $event): void {
            $this->io->println('wumpus-waked-up');
        });

        $game->addEventListener(function (ArrowRandomFlight $event): void {
            $this->io->println('random-arrow-flight');
        });

        $game->addEventListener(function (ArrowMissed $event): void {
            $this->io->println('MISSED');
        });

        /* Main loop */

        sense_room:

        $game->sensePlayerRoom($playerId);

        shoot_or_move:

        $this->io->println();

        $command = strtoupper(trim($this->io->input('input-shoot-or-move')));

        if ($command === 'M') {
            $whereTo = $this->io->input('input-where-to');
            $game->move($playerId, $whereTo);
        }

        if ($command === 'S') {
            arrow_energy:
            $arrowEnergy = $this->io->input(
                'input-no-of-rooms',
                '',
                [
                    'max_arrow_energy' => $this->config->getParam('arrow_max_energy')
                ]
            );

            if (
                $arrowEnergy < 1
                || $arrowEnergy > $this->config->getParam('arrow_max_energy')
            ) {
                goto arrow_energy;
            }

            $arrowTrajectory = [];
            for ($i = 0; $i < $arrowEnergy; $i++) {
                arrow_trajectory:
                $room = $this->io->input('input-room');
                if (
                    $i > 1
                    && $room == $arrowTrajectory[$i - 2]
                ) {
                    $this->io->println('invalid-arrow-trajectory');
                    goto arrow_trajectory;
                }

                if (!$world->existRoom($room)) {
                    $this->io->println('invalid-shoot-room', ['max_rooms' => $world->getNumRooms()]);
                    goto arrow_trajectory;
                }
                $arrowTrajectory[] = $room;
            }

            $game->shoot($playerId, $arrowTrajectory);

            if (!$player->isGameOver()) {
                goto shoot_or_move;
            }
        }

        if (!$player->isGameOver()) {
            goto sense_room;
        }

        if ($player->isGotWumpus()) {
            $this->io->println('win-game-over');
        } else {
            $this->io->println('loose-game-over');
        }

        goto main_menu;
    }
}
