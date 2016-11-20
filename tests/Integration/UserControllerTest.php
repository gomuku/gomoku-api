<?php

namespace Tests\Integration;

use \Firebase\JWT\JWT;

class UserControllerTest extends BaseTestCase
{
    /**
     * setUp
     */
    public function setUp()
    {
        parent::setUp();
        $this->table('User')->create();
        $this->table('Role')->create();
        $this->table('Capability')->create();
        $this->table('RoleAndCapability')->create();
    }

    /**
     * tearDown
     */
    public function tearDown()
    {
        $this->table('User')->drop();
        $this->table('Role')->drop();
        $this->table('Capability')->drop();
        $this->table('RoleAndCapability')->drop();
    }

    /**
     * Insert admin user data
     */
    protected function _insertAdminUser($users)
    {
        $this->table('Capability')->insert([
            [ 'id' => 1, 'capability_name' => 'read'],
            [ 'id' => 2, 'capability_name' => 'write'],
            [ 'id' => 3, 'capability_name' => 'delete']
        ]);
        $this->table('RoleAndCapability')->insert([
            ['role_id' => 1, 'capability_id' => 1, 'allowed' => true],
            ['role_id' => 1, 'capability_id' => 2, 'allowed' => true],
            ['role_id' => 1, 'capability_id' => 3, 'allowed' => true]
        ]);
        $this->table('Role')->insert([
            ['id' => 1, 'role_name' => 'admin']
        ]);
        $this->table('User')->insert($users);
    }

    /**
     * Test that the index route returns a rendered response containing the text 
     * 'SlimFramework' but not a greeting
     */
    public function testUserGenTokenOnSuccess()
    {
        // GIVEN
        $this->_insertAdminUser([
            [
                'username' => 'vkiet',
                'password' => md5('123456'),
                'email'    => 'vkiet@example.com',
                'fullname' => 'Kiet',
                'role_id'  => 1
            ]
        ]);

        // WHEN
        $dataTest = [
            'username' => 'vkiet',
            'password' => '123456',
            'scopes'   => ['read', 'write', 'delete']
        ];
        $response = $this->request('POST', '/token', $dataTest);
        $config   = (object) $this->ci->get('settings')['token'];
        $expected = json_encode([
            'code'   => 200,
            'status' => 'OK',
            'token'  => JWT::encode($dataTest, $config->secret, $config->algorithm)
        ]);

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains($expected, (string) $response->getBody());
    }

    /**
     * Test that the index route returns a rendered response containing the text 
     * 'SlimFramework' but not a greeting
     */
    public function testUserGenTokenOnUnauthorized()
    {
        // GIVEN
        $this->table('User')->insert([
            [
                'username' => 'vkiet',
                'password' => md5('123456'),
                'email'    => 'vkiet@example.com',
                'fullname' => 'Kiet'
            ]
        ]);

        // WHEN
        $dataTest = [
            'username' => 'vkiet OR 1=1',
            'password' => '',
            'scopes'   => ['read', 'write', 'delete']
        ];
        $response = $this->request('POST', '/token', $dataTest);
        $expected = json_encode([
            'code'    => 401,
            'status'  => 'NG',
            'message' => 'Wrong username or password.'
        ]);

        // THEN
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertContains($expected, (string) $response->getBody());
    }

}
