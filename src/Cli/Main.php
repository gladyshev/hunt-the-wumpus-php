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
use Htw\GameRules\World;
use Htw\GameRules\WorldObjectFactory;
use Htw\GameRules\WorldObjectInterface;
use Htw\IO\IOInterface;
use Htw\GameRules\WorldObjects\Pit;
use Htw\GameRules\WorldObjects\Player;
use Htw\Config\ConfigInterface;
use Htw\GameRules\Game;

final class Main
{
    private const STARTTEXT = <<<EOD
     ATTENTION ALL WUMPUS LOVERS!!!
     THERE ARE NOW TWO ADDITIONS TO THE WUMPUS FAMILY
     OF PROGRAMS.

      WUMP2:  SOME DIFFERENT CAVE ARRANGEMENTS
      WUMP3:  DIFFERENT HAZARDS
EOD;

    private const INSTRUCTIONS = <<<EOD

WELCOME TO 'HUNT THE WUMPUS'

THE WUMPUS LIVES IN A CAVE OF 20 ROOMS. EACH ROOM
HAS 3 TUNNELS LEADING TO OTHER ROOMS. (LOOK AT A
DODECAHEDRON TO SEE HOW THIS WORKS-IF YOU DON'T KNOW
WHAT A DODECAHEDRON IS, ASK SOMEONE)

    HAZARDS:
BOTTOMLESS PITS - TWO ROOMS HAVE BOTTOMLESS PITS IN THEM
    IF YOU GO THERE, YOU FALL INTO THE PIT (& LOSE!)
SUPER BATS - TWO OTHER ROOMS HAVE SUPER BATS. IF YOU
    GO THERE, A BAT GRABS YOU AND TAKES YOU TO SOME OTHER
    ROOM AT RANDOM. (WHICH MIGHT BE TROUBLESOME)

    WUMPUS:
THE WUMPUS IS NOT BOTHERED BY THE HAZARDS (HE HAS SUCKER
FEET AND IS TOO BIG FOR A BAT TO LIFT).  USUALLY
HE IS ASLEEP. TWO THINGS WAKE HIM UP: YOUR ENTERING
HIS ROOM OR YOUR SHOOTING AN ARROW.
    IF THE WUMPUS WAKES, HE MOVES (P=.75) ONE ROOM
OR STAYS STILL (P=.25). AFTER THAT, IF HE IS WHERE YOU
ARE, HE EATS YOU UP (& YOU LOSE!)

    YOU:
EACH TURN YOU MAY MOVE OR SHOOT A CROOKED ARROW
    MOVING: YOU CAN GO ONE ROOM (THRU ONE TUNNEL)
    ARROWS: YOU HAVE 5 ARROWS. YOU LOSE WHEN YOU RUN OUT.
    EACH ARROW CAN GO FROM 1 TO 5 ROOMS. YOU AIM BY TELLING
THE COMPUTER THE ROOM#S YOU WANT THE ARROW TO GO TO.
    IF THE ARROW CAN'T GO THAT WAY (IE NO TUNNEL) IT MOVES
AT RANDOM TO THE NEXT ROOM.
    IF THE ARROW HITS THE WUMPUS, YOU WIN.
    IF THE ARROW HITS YOU, YOU LOSE.

    WARNINGS:
    WHEN YOU ARE ONE ROOM AWAY FROM WUMPUS OR HAZARD,
THE COMPUTER SAYS:
    WUMPUS  -  'I SMELL A WUMPUS'
    BAT     -  'BATS NEARBY'
    PIT     -  'I FEEL A DRAFT'
 
EOD;

    private ConfigInterface $config;
    private IOInterface $io;
    private WorldObjectFactory $worldObjectFactory;

    public function __construct(
        IOInterface $io,
        ConfigInterface $config,
        WorldObjectFactory $worldObjectFactory
    ) {
        $this->io = $io;
        $this->config = $config;
        $this->worldObjectFactory = $worldObjectFactory;
    }

    /**
     * @param int $playerId
     * @throws \ReflectionException
     */
    public function start(int $playerId = 1): void
    {
        $this->io->println(self::STARTTEXT);
        $this->io->println();

        main_menu:

        $command = strtoupper(trim($this->io->input("ENTER TO START NEW GAME, 'I' TO INSTRUCTIONS, 'Q' TO QUIT ")));

        if ($command === 'I') {
            $this->io->println(self::INSTRUCTIONS);
        }

        if ($command === 'Q') {
            $this->io->println('BYE-BYE');
            return;
        }

        $playerName = $this->io->input('WHAT IS YOUR NAME? ');


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
                $hazardType,
                rand(100, getrandmax())
            );
        }

        $world = World::create($map, $worldObjects);

        $theGame = new Game($world);

        $theGame->addEventListener(function (InvalidMove $event): void {
            $this->io->println('INVALID ROOM');
        });

        $theGame->addEventListener(function (LeadRoomHazard $event): void {
            if ($event->getHazard()->getType() === Pit::TYPE_PIT) {
                $this->io->println('I FEEL A DRAFT');
            }

            if ($event->getHazard()->getType() === Pit::TYPE_BAT) {
                $this->io->println('BATS NEARBY!');
            }

            if ($event->getHazard()->getType() === Pit::TYPE_WUMPUS) {
                $this->io->println('I SMELL A WUMPUS!');
            }
        });

        $theGame->addEventListener(function (YouAreInRoom $event): void {
            $this->io->println("YOU ARE IN ROOM {room}", [
                'room' => $event->getRoom()
            ]);

            $this->io->println('TUNNELS LEADS TO {room1} {room2} {room3}', [
                'room1' => $event->getLeadRooms()[0],
                'room2' => $event->getLeadRooms()[1],
                'room3' => $event->getLeadRooms()[2],
            ]);
        });

        $theGame->addEventListener(function (SuperBatSnatch $event): void {
            $this->io->println('ZAP--SUPER BAT SNATCH! ELSEWHEREVILLE FOR YOU!');
        });

        $theGame->addEventListener(function (WumpusGotYou $event): void {
            $this->io->println("TSK TSK TSK- WUMPUS GOT YOU!");
        });
        
        $theGame->addEventListener(function (OutOfArrows $event): void {
            $this->io->println("OUT OF ARROWS.");
        });

        $theGame->addEventListener(function (FellInPit $event): void {
            $this->io->println("YYYIIIIEEEE . . . FELL IN PIT");
        });

        $theGame->addEventListener(function (ArrowHit $arrowHit): void {
            $type = $arrowHit->getWorldObject()->getType();
            switch ($type) {
                case WorldObjectInterface::TYPE_WUMPUS:
                    $this->io->println('AHA! YOU GOT THE WUMPUS!');
                    break;

                case WorldObjectInterface::TYPE_PLAYER:
                    $this->io->println('OUCH! ARROW GOT YOU!');
            }
        });

        $theGame->addEventListener(function (WumpusWakedUp $event): void {
            $this->io->println('...OOPS! BUMPED A WUMPUS!');
        });

        $theGame->addEventListener(function (ArrowRandomFlight $event): void {
            $this->io->println('NO TUNNEL FOR ARROW. ARROW FLEW GOD KNOWS WHERE!');
        });

        $theGame->addEventListener(function (ArrowMissed $event): void {
            $this->io->println('MISSED');
        });

        /* Main loop */

        sense_room:

        $theGame->sensePlayerRoom($playerId);

        shoot_or_move:

        $command = strtoupper(trim($this->io->input('SHOOT OR MOVE (S-M)')));

        if ($command === 'M') {
            $whereTo = $this->io->input('WHERE TO?');
            $theGame->move($playerId, $whereTo);
        }

        if ($command === 'S') {
            arrow_energy:
            $arrowEnergy = $this->io->input('NO. OF ROOMS (1-{max_arrow_energy})', '', [
                'max_arrow_energy' => $this->config->getParam('arrow_max_energy')
            ]);

            if (
                $arrowEnergy < 1
                || $arrowEnergy > $this->config->getParam('arrow_max_energy')
            ) {
                goto arrow_energy;
            }

            $arrowTrajectory = [];
            for ($i = 0; $i < $arrowEnergy; $i++) {
                arrow_trajectory:
                $room = $this->io->input('ROOM #');
                if (
                    $i > 1
                    && $room == $arrowTrajectory[$i - 2]
                ) {
                    $this->io->println('ARROWS AREN\'T THAT CROOKED - TRY ANOTHER ROOM');
                    goto arrow_trajectory;
                }

                if (!$world->existRoom($room)) {
                    $this->io->println('ONLY 1-{max_rooms}', ['max_rooms' => $world->getNumRooms()]);
                    goto arrow_trajectory;
                }
                $arrowTrajectory[] = $room;
            }

            $theGame->shoot($playerId, $arrowTrajectory);

            if (!$player->isGameOver()) {
                goto shoot_or_move;
            }
        }

        if (!$player->isGameOver()) {
            goto sense_room;
        }

        if ($player->isGotWumpus()) {
            $this->io->println('HEE HEE HEE - THE WUMPUS\'LL GETCHA NEXT TIME!!');
        } else {
            $this->io->println('HA HA HA - YOU LOSE!');
        }

        goto main_menu;
    }
}
