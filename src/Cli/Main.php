<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\Cli;

use Htw\IO\IOInterface;
use Htw\Config\ConfigInterface;

final class Main
{
    private const COMMAND_QUIT          = 'Q';
    private const COMMAND_INSTRUCTIONS  = 'I';
    private const COMMAND_SHOOT         = 'S';
    private const COMMAND_MOVE          = 'M';

    private ConfigInterface $config;
    private IOInterface $io;
    private GameFactory $gameFactory;

    public function __construct(
        ConfigInterface $config,
        IOInterface $io,
        GameFactory $gameFactory
    ) {
        $this->io = $io;
        $this->config = $config;
        $this->gameFactory = $gameFactory;
    }

    public function start(int $playerId, string $playerName): void
    {
        $this->io->println('welcome');
        $this->io->println('disclaimer');

        main_menu:

        $command = $this->io->inputln('cli.welcome');

        if ($command === self::COMMAND_INSTRUCTIONS) {
            $this->io->println('instructions');
            goto main_menu;
        }

        if ($command === self::COMMAND_QUIT) {
            $this->io->println('exit');
            return;
        }

        $this->io->println('start-game');

        /* Main loop */

        sense_room:

        $this->io->println();

        $game = $this->gameFactory->createGame(
            $playerName,
            $playerId,
            $this->io
        );

        $game->sensePlayerRoom($playerId);

        shoot_or_move:

        $command = $this->io->inputln('input-shoot-or-move');

        if ($command === self::COMMAND_MOVE) {
            $whereTo = $this->io->inputln('input-where-to');
            $game->move($playerId, $whereTo);
        }

        if ($command === self::COMMAND_SHOOT) {
            arrow_energy:
            $arrowEnergy = $this->io->inputln(
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
                $room = $this->io->inputln('input-room');
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
