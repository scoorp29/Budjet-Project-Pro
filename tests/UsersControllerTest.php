<?php
/**
 * Created by PhpStorm.
 * User: Ecole-IPPSI
 * Date: 30/01/2019
 * Time: 14:41
 */

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UsersControllerTest extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        exec('php bin/console hautelook:fixtures:load --append');

        parent::setUpBeforeClass();

    }

    /**
     * @group SuccesUser
     */
    public function testGetApiUserProfile()
    {
        $client = static::createClient();
        $client->request('GET', '/api/user/profile', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '93324'
            ]
        );

        $response = $client->getResponse();
        $userjson = $response->getContent();

        $user = \json_decode($userjson, true);

        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('firstname', $user);
        $this->assertArrayHasKey('lastname', $user);
        $this->assertArrayHasKey('email', $user);
        $this->assertArrayHasKey('adress', $user);
        $this->assertArrayHasKey('country', $user);
        $this->assertArrayHasKey('roles', $user);
        $this->assertArrayHasKey('apiKey', $user);
        $this->assertArrayHasKey('subscription', $user);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @group SuccesUser
     */
    public function testPatchApiUserProfile()
    {
        $client = static::createClient();
        $client->request('PATCH', '/api/user/profile', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '99445'
            ],
            '{
            "firstname": "NewName",
            "lastname": "NewLastName",
            "adress": "NewAdress",
            "country": "FR",
            "subscription": {
		                        "id": 4
	                         }            
            }'
        );

        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($content);
    }

    //Test Fail Patch User : wrong country

    /**
     * @group FailUser
     */
    public function testFailPatchApiUserProfile()
    {
        $client = static::createClient();
        $client->request('PATCH', '/api/user/profile', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '99445'
            ],
            '{
            "firstname": "NewName",
            "lastname": "NewLastName",
            "adress": "NewAdress",
            "country": "FailCountry",
            "subscription": {
		                        "id": 4
	                         }            
            }'
        );

        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($content);
    }
}