<?php

namespace tests\Integration;

class RoomControllerTest extends BaseTestCase
{
    /**
     * login token.
     *
     * @var string
     */
    protected $token;

    /**
     * setUp.
     */
    public function setUp()
    {
        parent::setUp();

        // create table
        $this->table('User')->create();
        $this->table('Role')->create();
        $this->table('Capability')->create();
        $this->table('RoleAndCapability')->create();
        $this->table('Room')->create();

        // insert default data
        $this->table('Capability')->insert([
            ['id' => 1, 'capability_name' => 'read'],
            ['id' => 2, 'capability_name' => 'write'],
            ['id' => 3, 'capability_name' => 'delete'],
        ]);
        $this->table('Role')->insert([
            ['id' => 1, 'role_name' => 'admin'],
        ]);
        $this->table('RoleAndCapability')->insert([
            ['role_id' => 1, 'capability_id' => 1, 'allowed' => true],
            ['role_id' => 1, 'capability_id' => 2, 'allowed' => true],
            ['role_id' => 1, 'capability_id' => 3, 'allowed' => true],
        ]);
        $this->table('Room')->insert([
            ['id' => 1, 'name' => 'Room 1', 'player_ids' => '[1, 2]', 'viewer_ids' => '[3]', 'enable' => true],
            ['id' => 2, 'name' => 'Room 2', 'player_ids' => '', 'viewer_ids' => '', 'enable' => true]
        ]);
        $this->table('User')->insert([
            [
                'id'       => 1,
                'username' => 'vkiet',
                'password' => md5('123456'),
                'email'    => 'vkiet@example.com',
                'fullname' => 'Kiet',
                'role_id'  => 1
            ],
            [
                'id'       => 2,
                'username' => 'vkiet2',
                'password' => md5('123456'),
                'email'    => 'vkiet2@example.com',
                'fullname' => 'Kiet 2',
                'role_id'  => null
            ],
            [
                'id'       => 3,
                'username' => 'vkiet3',
                'password' => md5('123456'),
                'email'    => 'vkiet3@example.com',
                'fullname' => 'Kiet 3',
                'role_id'  => null
            ]
        ]);

        // login to get token
        $response = $this->request('POST', '/login', [
            'username' => 'vkiet',
            'password' => '123456'
        ]);
        $resData = json_decode($response->getBody());
        $this->token = $resData->token;
    }

    /**
     * tearDown.
     */
    public function tearDown()
    {
        $this->table('User')->drop();
        $this->table('Role')->drop();
        $this->table('Capability')->drop();
        $this->table('RoleAndCapability')->drop();
        $this->table('Room')->drop();
    }

    /**
     * testApiResponseListOfRooms.
     */
    public function testApiResponseRoom()
    {
        // GIVEN
        $headers = ['HTTP_AUTHORIZATION' => 'Bearer '.$this->token];

        // WHEN
        $response = $this->request('GET', '/rooms/1', null, $headers);

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
        $resData = json_decode($response->getBody());

        $this->assertObjectHasAttribute('code', $resData);
        $this->assertObjectHasAttribute('status', $resData);
        $this->assertObjectHasAttribute('data', $resData);
        $this->assertEquals(200, $resData->code);
        $this->assertEquals('OK', $resData->status);

        $room = $resData->data;
        $this->assertEquals('Room 1', $room->name);
        $this->assertEquals([1, 2], $room->player_ids);
        $this->assertEquals([3], $room->viewer_ids);
        $this->assertEquals('vkiet', $room->players[0]->username);
        $this->assertEquals('vkiet2', $room->players[1]->username);
        $this->assertEquals('vkiet3', $room->viewers[0]->username);
    }

    /**
     * testApiResponseListOfRooms.
     */
    public function testApiResponseListRooms()
    {
        // GIVEN
        $headers = ['HTTP_AUTHORIZATION' => 'Bearer '.$this->token];

        // WHEN
        $response = $this->request('GET', '/rooms', null, $headers);

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
        $resData = json_decode($response->getBody());

        $this->assertObjectHasAttribute('code', $resData);
        $this->assertObjectHasAttribute('status', $resData);
        $this->assertObjectHasAttribute('data', $resData);
        $this->assertEquals(200, $resData->code);
        $this->assertEquals('OK', $resData->status);

        list($room) = $resData->data;
        $this->assertEquals('Room 1', $room->name);
        $this->assertEquals([1, 2], $room->player_ids);
        $this->assertEquals([3], $room->viewer_ids);
        $this->assertEquals('vkiet', $room->players[0]->username);
        $this->assertEquals('vkiet2', $room->players[1]->username);
        $this->assertEquals('vkiet3', $room->viewers[0]->username);
    }

    /**
     * testJoinToRoomSuccess
     */
    public function testPlayerJoinToRoomSuccess()
    {
        // GIVEN
        $headers = ['HTTP_AUTHORIZATION' => 'Bearer '.$this->token];

        // WHEN
        $response = $this->request('POST', '/rooms/join/2', ['type' => 'player'], $headers);

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
    }
}
