<?php

namespace Tests\Integration;

use Tests\Db\User as DbUser;
use Tests\Db\Token as DbToken;

class GetTokenTest extends BaseTestCase
{
	public function setUp(){
		parent::setUp();
		DbUser::create();
		DbToken::create();
	}

	/**
	 * [tearDown description]
	 * @return [type] [description]
	 */
	public function tearDown(){
		parent::tearDown();
		DbUser::drop();
		DbToken::drop();
	}

    /**
     * Test that the index route returns a rendered response containing the text 'SlimFramework' but not a greeting
     */
    public function testGetTokenOnFirstTimeLoginSuccess()
    {
    	// GIVEN
    	DbUser::insert([
    		[
    			'username' 	=> 'vkiet',
    			'password' 	=> md5('123456'),
    			'email' 	=> 'vkiet@tester@yahoo-corp.jp',
    			'fullname' 	=> 'Vo Anh Kiet'
    		]
    	]);

    	// WHEN
        $response = $this->request('POST', '/get_token', [
        	'username' => 'vkiet', 
        	'password' => '123456'
       	]);

       	// THEN
        $this->assertEquals(200, $response->getStatusCode());
       	$resData = json_decode($response->getBody());
        $this->assertIsMd5String($resData->token);
        $this->assertEquals("vkiet", $resData->username);
        $this->assertInternalType("int", $resData->expired);
    }

    /**
     * Test that the index route returns a rendered response containing the text 'SlimFramework' but not a greeting
     */
    public function testGetTokenOnFirstTimeLoginFail()
    {
    	// GIVEN
    	// -->db is empty data
    	
    	// WHEN
        $response = $this->request('POST', '/get_token', [
        	'username' => 'vkiet', 
        	'password' => '123456'
       	]);

       	// THEN
        $this->assertEquals(401, $response->getStatusCode());
       	$resData = json_decode($response->getBody());
        $this->assertEquals(401, $resData->error->code);
        $this->assertEquals('Wrong username or password. access denied.', $resData->error->message);
    }

    
}