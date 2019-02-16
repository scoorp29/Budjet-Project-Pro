<?php
/**
 * Created by PhpStorm.
 * User: Ecole-IPPSI
 * Date: 31/01/2019
 * Time: 17:19
 */

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AnonymousControllerTest extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        exec('php bin/console hautelook:fixtures:load --purge-with-truncate');

        parent::setUpBeforeClass();

    }

    /**
     * @group SuccesAnonymous
     * @group test
     */
    public function testGetAnonymousUserProfile()
    {
        $client = static::createClient();
        $client->request('GET', '/api/profile/2', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
            ]
        );
        $response = $client->getResponse();
        $userjson = $response->getContent();

        $user = \json_decode($userjson, true);

        $this->assertArrayHasKey('firstname', $user);
        $this->assertArrayHasKey('lastname', $user);
        $this->assertArrayHasKey('email', $user);
        $this->assertArrayHasKey('subscription', $user);
        $this->assertArrayHasKey('cards', $user);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @group SuccesAnonymous
     */
    public function testPostApiNewUser()
    {
        $client = static::createClient();
        $client->request('POST', '/api/new/user', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
            ],
            '{ 
            "firstname": "NewUserName",
            "lastname": "NewUserLastName",
            "email": "NewUserLastName@gmail.com",
            "adress": "NewUserAdress",
            "country": "FR",
            "subscription": {
		                        "id": 4
	                         }
            }'
        );

        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJson($content);
    }

    //Test : email non valide
    /**
     * @group FailAnonymous
     */
    //We test if all the conditions are fulfilled (Assert in Entity / User)
    public function testPostFailApiNewUser()
    {
        $client = static::createClient();
        $client->request('POST', '/api/new/user', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
            ],
            '{ 
            "firstname": "NewUserName",
            "lastname": "NewUserLastName",
            "email": "EmailNonValide",
            "adress": "NewUserAdress",
            "country": "FR",
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

    //Test : country non valide
    /**
     * @group FailAnonymous
     */
    //We test if all the conditions are fulfilled (Assert in Entity / User)
    public function testPostFailCountryApiNewUser()
    {
        $client = static::createClient();
        $client->request('POST', '/api/new/user', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
            ],
            '{ 
            "firstname": "NewUserName",
            "lastname": "NewUserLastName",
            "email": "emailmail@user.com",
            "adress": "NewUserAdress",
            "country": "CountryNonValide",
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