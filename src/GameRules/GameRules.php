<?php
/**
 * @project Hunt the Wumpus
 */

namespace Wumpus\GameRules;

final class GameRules
{
    public const EXIT_CODE_WIN = 1;
    public const EXIT_CODE_LOSE = 0;

    private DataStorageInterface $storage;
    private UIInterface $io;
    private ConfigInterface $config;

    private const HELP = <<<EOD

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

    public function __construct(
        DataStorageInterface $storage,
        UIInterface $language,
        ConfigInterface $config
    ) {
        $this->storage = $storage;
        $this->io = $language;
        $this->config = $config;
    }


    public function start(): int
    {
        $cave = new Cave(
            $this->config->getParam('map'),
            $this->config->getParam('hazards')
        );


        $player = new Player(
            $cave->getRandomFreeRoom(),
            $this->config->getParam('arrows')
        );

        $this->io->println('HUNT THE WUMPUS');
        $this->io->println();

        main_menu:

        $main_menu = $this->io->input('START GAME? TYPE \'H\' TO HELP OR ENTER TO START ');

        if ($main_menu === 'H') {
            $this->io->println(self::HELP);
            goto main_menu;
        }

        start_game:

        if (!$player->hasArrow()) {
            $this->io->println("OUT OF ARROWS.\nHA HA HA - YOU LOOSE!");
            return self::EXIT_CODE_LOSE;
        }

        if ($cave->roomHasHazard('wumpus', $player->getRoom())) {
            $this->io->println('TSK TSK TSK - WUMPUS GOT YOU!');
            return self::EXIT_CODE_LOSE;
        }

        if ($cave->roomHasHazard('bat', $player->getRoom())) {
            $this->io->println('ZAP--SUPER BAT SNATCH! ELSEWHEREVILLE FOR YOU!');
            return self::EXIT_CODE_LOSE;
        }

        if ($cave->roomHasHazard('pit', $player->getRoom())) {
            $this->io->println("YYYIIIIEEEE . . . FELL IN PIT");
            return self::EXIT_CODE_LOSE;
        }

        $lead_rooms = $cave->getLeadRooms($player->getRoom());

        $this->io->println("YOU ARE IN ROOM {$player->getRoom()}");
        $this->io->println("TUNNELS LEAD TO " . implode(" ", $lead_rooms));

        foreach ($lead_rooms as $lead_room) {
            if ($cave->roomHasHazard('wumpus', $lead_room)) {
                $this->io->println("I SMELL A WUMPUS!");
            }

            if ($cave->roomHasHazard('bat', $lead_room)) {
                $this->io->println('BATS NEARBY!');
            }

            if ($cave->roomHasHazard('pit', $lead_room)) {
                $this->io->println('I FEEL A DRAFT');
            }
        }

        shoot_or_move:

        $choice = $this->io->input("SHOOT OR MOVE (S/M)? ");

        if (!in_array($choice, ['S', 'M'])) {
            $this->io->println('ONLY "S" OR "M"');
            goto shoot_or_move;
        }

        if ($choice === 'M') {
            where_to:

            $room = $this->io->input("WHERE TO? ");

            if (!in_array($room, $lead_rooms)) {
                $this->io->println('ONLY ' . implode(', ', $lead_rooms));
                goto where_to;
            }

            $player->move($room);

            goto start_game;
        }

        if ($choice === 'S') {
            distance:
            $distance = $this->io->input("NO. OF ROOMS(1-5) ");
            if (
                !is_numeric($distance)
                || $distance < 1
                || $distance > 5
            ) {
                $this->io->println('ONLY 1-5 ALLOWED');
                goto distance;
            }

            $prev_arrow_room = $player->getRoom();

            for ($i = 0; $i < $distance; $i++) {
                arrow_room:
                $arrow_room = $this->io->input('ROOM #?');

                if (!is_numeric($arrow_room)) {
                    $this->io->println("ONLY 1-{$cave->getNumRooms()}");
                    goto arrow_room;
                }

                if ($prev_arrow_room == $arrow_room) {
                    $this->io->println("ARROWS AREN'T THAT CROOKED - TRY ANOTHER ROOM");
                    goto arrow_room;
                }

                $prev_arrow_room = $arrow_room;

                if (!$cave->existTunnel($arrow_room, $arrow_room)) {
                    $this->io->println('RANDOM ARROW FLIGHT');
                    $arrow_room = $cave->getRandomLeadRooms($arrow_room);
                }

                if ($cave->roomHasHazard('wumpus', $arrow_room)) {
                    $this->io->println("AHA! YOU GOT THE WUMPUS!");
                    return self::EXIT_CODE_WIN;
                }

                if ($player->getRoom() === $arrow_room) {
                    $this->io->println("OUCH! ARROW GOT YOU!");
                    return self::EXIT_CODE_LOSE;
                }

                foreach ($cave->getLeadRooms($arrow_room) as $lead_room) {
                    if (
                        $cave->roomHasHazard('wumpus', $lead_room)
                        && 0 > rand(0, 3) // P = .75
                    ) {
                        $this->io->println("...OOPS! BUMPED A WUMPUS!");
                        $new_wumpus_room = $cave->getRandomLeadRooms($lead_room);
                        if ($new_wumpus_room === $player->getRoom()) {
                            $this->io->println("TSK TSK TSK - WUMPUS GOT YOU!");
                            return self::EXIT_CODE_LOSE;
                        }
                        $cave->moveWumpusTo($lead_room, $new_wumpus_room);
                    }
                }
            }
        }

        throw new \RuntimeException('GAME LOGIC CRITICAL ERROR!');
    }
}