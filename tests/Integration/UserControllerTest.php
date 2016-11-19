<?php

namespace Tests\Integration;

use \Firebase\JWT\JWT;
use Tests\Db\User as DbUser;

class UserControllerTest extends BaseTestCase
{

    /**
     *
     * @var type 
     */
    public $dbUser;

    /**
     *
     * @var type 
     */
    public $dbToken;

    /**
     * setUp
     */
    public function setUp()
    {
        parent::setUp();

        $this->dbUser = new DbUser();
        $this->dbUser->create();
    }

    /**
     * tearDown
     */
    public function tearDown()
    {
        $this->dbUser->drop();
    }

    /**
     * Test that the index route returns a rendered response containing the text 
     * 'SlimFramework' but not a greeting
     */
    public function testUserGenTokenOnSuccess()
    {
        // GIVEN
        $this->dbUser->insert([
            [
                'username' => 'vkiet',
                'password' => md5('123456'),
                'email'    => 'vkiet@example.com',
                'fullname' => 'Kiet'
            ]
        ]);

        // WHEN
        $dataTest = [ 'username' => 'vkiet', 'password' => '123456'];
        $response = $this->request('POST', '/token', $dataTest);
        $secret   = $this->ci->get('settings')['token']['secret'];
        $expected = json_encode([
            'code'   => 200,
            'status' => 'OK',
            'token'  => JWT::encode($dataTest, $secret)
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
        $this->dbUser->insert([
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
            'password' => ''
        ];
        $response = $this->request('POST', '/token', $dataTest);
        $secret   = $this->ci->get('settings')['token']['secret'];
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
