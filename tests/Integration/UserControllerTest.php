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

        // create table
        $this->table('User')->create();
        $this->table('Role')->create();
        $this->table('Capability')->create();
        $this->table('RoleAndCapability')->create();

        // insert default data
        $this->_insertAdminUser([
            [
                'username' => 'vkiet',
                'password' => md5('123456'),
                'email'    => 'vkiet@example.com',
                'fullname' => 'Kiet',
                'role_id'  => 1
            ]
        ]);
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
     * testUserLoginOnSuccess
     */
    public function testUserLoginSuccess()
    {
        // GIVEN
        $dataTest = [
            'username' => 'vkiet',
            'password' => '123456',
            'scopes'   => ['read', 'write', 'delete']
        ];
        $config   = (object) $this->ci->get('settings')['token'];

        // WHEN
        $response = $this->request('POST', '/login', $dataTest);

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
        $resData = json_decode($response->getBody());
        $this->assertEquals(200, $resData->code);
        $this->assertEquals('OK', $resData->status);
        $this->assertRegExp('/^[0-9a-z._-]+$/i', $resData->token);
        return $resData;
    }

    /**
     * testUserLoginFaildWithSqlInjection
     */
    public function testUserLoginFailedOnInputSqlInjection()
    {
        // GIVEN
        $dataTest = [
            'username' => 'vkiet OR 1=1',
            'password' => '',
            'scopes'   => ['read', 'write', 'delete']
        ];

        // WHEN
        $response = $this->request('POST', '/login', $dataTest);

        // THEN
        $this->assertEquals(401, $response->getStatusCode());
        $resData = json_decode($response->getBody());
        $this->assertEquals(401, $resData->code);
        $this->assertEquals('NG', $resData->status);
        $this->assertEquals('Wrong username or password.', $resData->message);
    }

    /**
     * testUserGenerateTokenSuccess
     * @depends testUserLoginSuccess
     */
    public function testUserGenerateTokenSuccess($resOnLoginSuccess)
    {
        // GIVEN        
        $token = $resOnLoginSuccess->token;
        $headers = [ "HTTP_AUTHORIZATION" => "Bearer " . $token ];
        $dataTest = ['scopes' => ['read', 'write', 'delete']];

        // WHEN
        $response = $this->request('POST', '/token', $dataTest, $headers);

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
        $resData = json_decode($response->getBody());
        $this->assertEquals(200, $resData->code);
        $this->assertEquals('OK', $resData->status);
        $this->assertRegExp('/^[0-9a-z._-]+$/i', $resData->token);
    }

    /**
     * testUserGenerateTokenFailedOnInValidToken
     */
    public function testUserGenerateTokenFailedOnExpiredToken()
    {
        // GIVEN
        $time = time();
        $config   = (object) $this->ci->get('settings')['token'];
        $payload = [
            'iss'  => 'gomoku.api',
            'iat ' => $time,
            'nbf'  => $time,
            'exp'  => strtotime('-2 hours', $time),
            'data' => []
        ];
        $token = JWT::encode($payload, $config->secret, $config->algorithm);
        $headers = [ "HTTP_AUTHORIZATION" => "Bearer " . $token ];
        $dataTest = ['scopes' => ['read', 'write', 'delete']];

        // WHEN
        $response = $this->request('POST', '/token', $dataTest, $headers);

        // THEN
        $this->assertEquals(401, $response->getStatusCode());
        $resData = json_decode($response->getBody());
        $this->assertEquals(401, $resData->code);
        $this->assertEquals('NG', $resData->status);
        $this->assertEquals('Expired token', $resData->message);
    }

}
