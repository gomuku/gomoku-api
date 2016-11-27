<?php

namespace Tests\Integration;

use Firebase\JWT\JWT;

class HomepageTest extends BaseTestCase
{
    /**
     * Test that the index route returns a rendered response containing the text 'SlimFramework' but not a greeting
     */
    public function testGetHomepageWithoutName()
    {
        $config  = (object) $this->ci->get('settings')['token'];
        $payload = [
            'username' => 'vkiet', 
            'password' => '123456',
            'scopes' => ['read', 'write', 'delete']
        ];
        $token   = JWT::encode($payload, $config->secret, $config->algorithm);
        
        $response = $this->request('GET', '/', null, [
            "HTTP_AUTHORIZATION" => "Bearer " . $token
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Hello World!', (string) $response->getBody());
    }

}
