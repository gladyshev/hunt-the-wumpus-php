<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\Cli;

use Htw\IO\IOInterface;
use Htw\Config\ConfigInterface;

final class Main
{
    private ConfigInterface $config;
    private IOInterface $io;
    private GameFactory $gameFactory;

    public function __construct(
        IOInterface $io,
        ConfigInterface $config,
        GameFactory $gameFactory
    ) {
        $this->io = $io;
        $this->config = $config;
        $this->gameFactory = $gameFactory;
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

        $game = $this->gameFactory->createGame(
            $playerName,
            $playerId,
            $this->io
        );

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

                if (!$game->existRoom($room)) {
                    $this->io->println('invalid-shoot-room', ['max_rooms' => $game->getNumRooms()]);
                    goto arrow_trajectory;
                }
                $arrowTrajectory[] = $room;
            }

            $game->shoot($playerId, $arrowTrajectory);

            if (!$game->isPlayerGameOver($playerId)) {
                goto shoot_or_move;
            }
        }

        if (!$game->isPlayerGameOver($playerId)) {
            goto sense_room;
        }

        if ($game->isPlayerGotWumpus($playerId)) {
            $this->io->println('win-game-over');
        } else {
            $this->io->println('loose-game-over');
        }

        goto main_menu;
    }
}
