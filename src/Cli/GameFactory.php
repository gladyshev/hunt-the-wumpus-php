<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\Cli;

use Htw\Config\ConfigInterface;
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
use Htw\GameRules\Game;
use Htw\GameRules\HazardFactory;
use Htw\GameRules\WorldFactory;
use Htw\GameRules\WorldObjects\Player;
use Htw\IO\IOInterface;

final class GameFactory
{
    private HazardFactory $worldObjectFactory;
    private WorldFactory $worldFactory;
    private ConfigInterface $config;

    public function __construct(
        ConfigInterface $config,
        HazardFactory $worldObjectFactory,
        WorldFactory $worldFactory
    ) {
        $this->config = $config;
        $this->worldObjectFactory = $worldObjectFactory;
        $this->worldFactory = $worldFactory;
    }

    public function createGame(
        string $playerName,
        string $playerId,
        IOInterface $io
    ): Game {
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

        $game->addEventListener(function (InvalidMove $event) use ($io): void {
            $io->println('invalid-move-room');
        });

        $game->addEventListener(function (LeadRoomPit $event) use ($io): void {
            $io->println('feel-draft');
        });

        $game->addEventListener(function (LeadRoomBat $event) use ($io): void {
            $io->println('feel-bats');
        });

        $game->addEventListener(function (LeadRoomWumpus $event) use ($io): void {
            $io->println('feel-wumpus');
        });

        $game->addEventListener(function (YouAreInRoom $event) use ($io): void {
            $io->println("you-room", [
                'room' => $event->getRoom()
            ]);

            $io->println('you-room-tunnels', [
                'room1' => $event->getLeadRooms()[0],
                'room2' => $event->getLeadRooms()[1],
                'room3' => $event->getLeadRooms()[2],
            ]);
        });

        $game->addEventListener(function (SuperBatSnatch $event) use ($io): void {
            $io->println('super-bat-snatch');
        });

        $game->addEventListener(function (WumpusGotYou $event) use ($io): void {
            $io->println("wumpus-got-you");
        });

        $game->addEventListener(function (OutOfArrows $event) use ($io): void {
            $io->println("out-of-arrows");
        });

        $game->addEventListener(function (FellInPit $event) use ($io): void {
            $io->println("fell-in-pit");
        });

        $game->addEventListener(function (PlayerGotWumpus $event) use ($io): void {
            $io->println('you-got-wumpus');
        });

        $game->addEventListener(function (ArrowHitPlayer $event) use ($io): void {
            $io->println('arrow-got-you');
        });

        $game->addEventListener(function (WumpusWakedUp $event) use ($io): void {
            $io->println('wumpus-waked-up');
        });

        $game->addEventListener(function (ArrowRandomFlight $event) use ($io): void {
            $io->println('random-arrow-flight');
        });

        $game->addEventListener(function (ArrowMissed $event) use ($io): void {
            $io->println('MISSED');
        });

        return $game;
    }
}