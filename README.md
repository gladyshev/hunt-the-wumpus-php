<p align="center">
    <img src="https://upload.wikimedia.org/wikipedia/ru/1/18/Wumpus2.png">
    <h1 align="center">Hunt the Wumpus</h1>
    <br>
</p>

Another yet implementation a classic text-based adventure game "[Hunt the Wumpus](https://en.wikipedia.org/wiki/Hunt_the_Wumpus)" developed by Gregory Yob back in 1973. Inspired by reading Robert Martin's Clean Architecture.

# How to Run

To run console version you need download code from git and run `bin/game`.
```bash
$ cd ~/games
$ git clone https://github.com/gladyshev/hunt-the-wumpus-php.git htw
$ cd htw 
$ composer install
$ ./bin/htw
```

# Gameplay

CLI gameplay example: 
```bash
HUNT THE WUMPUS

I SMELL A WUMPUS!
YOU ARE IN ROOM 6
TUNNELS LEADS TO 15 1 7

SHOOT OR MOVE (S-M)m 
WHERE TO?1
YOU ARE IN ROOM 1
TUNNELS LEADS TO 2 6 5

SHOOT OR MOVE (S-M)m
WHERE TO?6
I SMELL A WUMPUS!
YOU ARE IN ROOM 6
TUNNELS LEADS TO 15 1 7

SHOOT OR MOVE (S-M)m
WHERE TO?7
TSK TSK TSK- WUMPUS GOT YOU!
HA HA HA - YOU LOSE!
ENTER TO START NEW GAME, 'I' TO INSTRUCTIONS, 'Q' TO QUIT q    
BYE-BYE

```

# Links
- [Hunt the Wumpus wiki page](https://en.wikipedia.org/wiki/Hunt_the_Wumpus) 
- [Hunt The Wumpus (BASIC computer game, genesis of Wumpus, history)](http://www.atariarchives.org/bcc1/showpage.php?page=247)
