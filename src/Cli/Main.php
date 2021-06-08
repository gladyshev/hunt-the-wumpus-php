<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\Cli;

use Htw\GameRules\Events\FellInPit;
use Htw\GameRules\Events\InvalidRoom;
use Htw\GameRules\Events\LeadRoomHazard;
use Htw\GameRules\Events\OutOfArrows;
use Htw\GameRules\Events\SuperBatSnatch;
use Htw\GameRules\Events\WumpusGotYou;
use Htw\GameRules\Events\YouAreInRoom;
use Htw\GameRules\World;
use Htw\IO\IOInterface;
use Htw\GameRules\Hazard;
use Htw\GameRules\Player;
use Htw\Config\ConfigInterface;
use Htw\GameRules\Game;

final class Main
{
    private const HELP =<<<EOD

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

    public function __construct(
        IOInterface $io,
        ConfigInterface $config
    ) {
        $this->io = $io;
        $this->config = $config;
    }

    public function start(int $playerId = 1): void
    {
        $this->io->println('HUNT THE WUMPUS');
        $this->io->println();

        main_menu:

        $command = strtoupper(trim($this->io->input("ENTER TO START NEW GAME, 'H' TO HELP, 'Q' TO QUIT ")));

        if ($command === 'H') {
            $this->io->println(self::HELP);
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
            $worldObjects[] = Hazard::fromType($hazardType);
        }

        $theGame = new Game(
            World::create($map, $worldObjects)
        );

        $theGame->addEventListener(function (InvalidRoom $event) {
            $this->io->println('INVALID ROOM');
        });

        $theGame->addEventListener(function (LeadRoomHazard $event) {
            if ($event->getHazard()->getType() === Hazard::TYPE_PIT) {
                $this->io->println('I FEEL A DRAFT');
            }

            if ($event->getHazard()->getType() === Hazard::TYPE_BAT) {
                $this->io->println('BATS NEARBY!');
            }

            if ($event->getHazard()->getType() === Hazard::TYPE_WUMPUS) {
                $this->io->println('I SMELL A WUMPUS!');
            }
        });

        $theGame->addEventListener(function (YouAreInRoom $event) {
            $this->io->println("YOU ARE IN ROOM {room}", [
                'room' => $event->getRoom()
            ]);

            $this->io->println('TUNNELS LEADS TO {room1} {room2} {room3}', [
                'room1' => $event->getLeadRooms()[0],
                'room2' => $event->getLeadRooms()[1],
                'room3' => $event->getLeadRooms()[2],
            ]);
        });

        $theGame->addEventListener(function (SuperBatSnatch $event) {
            $this->io->println('ZAP--SUPER BAT SNATCH! ELSEWHEREVILLE FOR YOU!');
        });

        $theGame->addEventListener(function (WumpusGotYou $event) {
            $this->io->println("TSK TSK TSK- WUMPUS GOT YOU!");
        });
        
        $theGame->addEventListener(function (OutOfArrows $event) {
            $this->io->println("OUT OF ARROWS.\nHA HA HA - YOU LOSE!");
        });

        $theGame->addEventListener(function (FellInPit $event) {
            $this->io->println("YYYIIIIEEEE . . . FELL IN PIT");
        });

        /* Main loop */

        while (!$player->isGameOver()) {
            $this->io->println();
            $theGame->sensePlayerRoom($playerId);

            /* Executing commands */

            $command = strtoupper(trim($this->io->input('SHOOT OR MOVE (S-M)')));

            switch ($command) {
                case 'M':
                    $whereTo = $this->io->input('WHERE TO?');

                    $theGame->move($playerId, $whereTo);
                    break;
            }
        }


        $this->io->println('GAME OVER.');

        goto main_menu;
    }
}
