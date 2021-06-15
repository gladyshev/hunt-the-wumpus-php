<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\Tests\GameRules;

use http\Exception\RuntimeException;
use Htw\GameRules\World;
use Htw\GameRules\WorldObjectInterface;
use Htw\GameRules\WorldObjects\Bat;
use Htw\GameRules\WorldObjects\Pit;
use Htw\GameRules\WorldObjects\Player;
use Htw\GameRules\WorldObjects\Wumpus;
use PHPUnit\Framework\TestCase;
/**
 * @coversDefaultClass \Htw\GameRules\World
 */
class WorldTest extends TestCase
{
    private $world;
    private $referenceMap = [];
    private $referenceObjectMap = [];

    public function setUp(): void
    {
        $this->referenceMap = [
            1 => [2, 6, 5],
            2 => [3, 8, 1],
            3 => [4, 10, 2],
            4 => [5, 2, 3],
            5 => [1, 14, 4],
            6 => [15, 1, 7],
            7 => [17, 6, 8],
            8 => [7, 2, 9],
            9 => [18, 8, 10],
            10 => [9, 3, 11],
            11 => [19, 10, 12],
            12 => [11, 4, 13],
            13 => [20, 12, 14],
            14 => [5, 11, 13],
            15 => [6, 16, 14],
            16 => [20, 15, 17],
            17 => [16, 7, 18],
            18 => [17, 9, 19],
            19 => [18, 11, 20],
            20 => [19, 13, 16],
        ];

        $this->referenceObjectMap = [
            1 => new Player(5, 'Player', 1),
            2 => new Wumpus(2),
            3 => new Bat(3),
            4 => new Pit(4)
        ];

        $this->world = new World(
            $this->referenceMap,
            $this->referenceObjectMap
        );
    }

    /**
     * @covers ::moveRoomObject
     */
    public function testMoveRoomObject()
    {
        // Move valid
        $this->world->moveRoomObject(4, 10);

        $this->assertEmpty($this->world->getRoomObject(4));
        $this->assertIsObject($this->world->getRoomObject(10));
        $this->assertEquals(4, $this->world->getRoomObject(10)->getId());
    }

    public function testAddWorldObject()
    {

    }

    public function testGetNumRooms()
    {

    }

    public function testCleanRoom()
    {

    }

    public function testExistRoom()
    {

    }

    public function testGetRandomFreeRoom()
    {

    }

    /**
     * @covers ::getPlayer
     */
    public function testGetPlayer()
    {
        $player = $this->world->getPlayer(1);
        $this->assertInstanceOf(Player::class, $player);
        $this->assertEquals(1, $player->getId());

        $this->expectException(\RuntimeException::class);
        $this->world->getPlayer(1001);
    }

    /**
     * @covers ::getRoomObject
     */
    public function testGetRoomObject()
    {
        $pit = $this->world->getRoomObject(4);
        $this->assertInstanceOf(Pit::class, $pit);

        $emptyObject = $this->world->getRoomObject(19);
        $this->assertNull($emptyObject);
    }

    public function testExistTunnel()
    {

    }

    public function testGetLeadRooms()
    {
        $leadRooms = $this->world->getLeadRooms(1);
        $this->assertEquals($this->referenceMap[1], $leadRooms);
    }

    /**
     * @covers ::getPlayerRoom
     */
    public function testGetPlayerRoom()
    {
        $playerRoom = $this->world->getPlayerRoom(1);
        $this->assertEquals(1, $playerRoom);

        $this->expectException(\RuntimeException::class);
        $this->world->getPlayerRoom(1000);
    }

    /**
     * @covers ::getRandomLeadRoom
     */
    public function testGetRandomLeadRoom()
    {
        $leadRooms = $this->referenceMap[1];
        $room = $this->world->getRandomLeadRoom(1);
        $this->assertTrue(in_array($room, $leadRooms));

        $this->expectException(\RuntimeException::class);
        $this->world->getRandomLeadRoom(10000);
    }

    /**
     * @covers ::roomHasObject
     */
    public function testRoomHasObject()
    {
        $res = $this->world->roomHasObject(WorldObjectInterface::TYPE_PIT, 4);
        $this->assertEquals(true, $res);

        $res = $this->world->roomHasObject(WorldObjectInterface::TYPE_BAT, 4);
        $this->assertEquals(false, $res);

        $res = $this->world->roomHasObject(WorldObjectInterface::TYPE_PIT, 19);
        $this->assertEquals(false, $res);
    }
}
