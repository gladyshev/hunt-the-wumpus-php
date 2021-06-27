<?php

return [
    'welcome' => <<<EOD
HUNT THE WUMPUS
EOD,
    'disclaimer' => <<<EOD

ATTENTION ALL WUMPUS LOVERS!!!
THERE ARE NOW TWO ADDITIONS TO THE WUMPUS FAMILY
OF PROGRAMS.

    WUMP2:  SOME DIFFERENT CAVE ARRANGEMENTS
    WUMP3:  DIFFERENT HAZARDS

EOD,
    'instructions' => <<<EOD

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
 
EOD,
    'start-game' => <<<EOD
HUNT THE WUMPUS!
EOD,

    'cli.welcome' => "ENTER TO START NEW GAME, 'I' TO INSTRUCTIONS, 'Q' TO QUIT ",

    'exit' => 'BYE-BYE',
    'invalid-move-room' => 'INVALID ROOM',
    'feel-draft' => 'I FEEL A DRAFT',
    'feel-bats' => 'BATS NEARBY!',
    'feel-wumpus' => 'I SMELL A WUMPUS!',
    'you-room' => 'YOU ARE IN ROOM {room}',
    'you-room-tunnels' => 'TUNNELS LEADS TO {room1} {room2} {room3}',
    'super-bat-snatch' => 'ZAP--SUPER BAT SNATCH! ELSEWHEREVILLE FOR YOU!',
    'wumpus-got-you' => 'TSK TSK TSK- WUMPUS GOT YOU!',
    'fell-in-pit' => 'YYYIIIIEEEE . . . FELL IN PIT',
    'you-got-wumpus' => 'AHA! YOU GOT THE WUMPUS!',
    'arrow-got-you' => 'OUCH! ARROW GOT YOU!',
    'wumpus-waked-up' => '...OOPS! BUMPED A WUMPUS!',
    'random-arrow-flight' => 'NO TUNNEL FOR ARROW. ARROW FLEW GOD KNOWS WHERE!',
    'arrow-missed' => 'MISSED',
    'input-shoot-or-move' => 'SHOOT OR MOVE (S-M)',
    'input-where-to' => 'WHERE TO?',
    'input-no-of-rooms' => 'NO. OF ROOMS (1-{max_arrow_energy})',
    'input-room' => 'ROOM #',
    'invalid-arrow-trajectory' => 'ARROWS AREN\'T THAT CROOKED - TRY ANOTHER ROOM',
    'invalid-shoot-room' => 'ONLY 1-{max_rooms}',
    'win-game-over' => 'HEE HEE HEE - THE WUMPUS\'LL GETCHA NEXT TIME!!',
    'loose-game-over' => 'HA HA HA - YOU LOSE!'
];