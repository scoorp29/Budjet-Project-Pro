<?php
/**
 * Created by PhpStorm.
 * User: Ecole-IPPSI
 * Date: 01/02/2019
 * Time: 10:50
 */

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class CardControllerTest extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        exec('php bin/console hautelook:fixtures:load --append');

        parent::setUpBeforeClass();
    }

    /*Test One Card*/
    /**
     * @group SuccesCard
     */
    public function testGetApiAdminOneCard()
    {
        $client = static::createClient();
        $client->request('GET', '/api/admin/cards/3', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '72312'
            ]
        );

        $response = $client->getResponse();
        $content = $response->getContent();

        $card = \json_decode($content, true);

        $this->assertArrayHasKey('id', $card);
        $this->assertArrayHasKey('name', $card);
        $this->assertArrayHasKey('creditCardType', $card);
        $this->assertArrayHasKey('creditCardNumber', $card);
        $this->assertArrayHasKey('currencyCode', $card);
        $this->assertArrayHasKey('value', $card);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /*Test All Card Admin*/
    /**
     * @group SuccesCard
     */
    public function testGetApiAllAdminCard()
    {
        $client = static::createClient();
        $client->request('GET', '/api/admin/my/cards', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '72312'
            ]
        );
        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($content);

        $arrayContent = \json_decode($content, true);
        $this->assertCount(2, $arrayContent);
    }

    /*Test All Card*/
    /**
     * @group SuccesCard
     */
    public function testGetApiAdminAllCard()
    {
        $client = static::createClient();
        $client->request('GET', '/api/admin/cards', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '72312'
            ]
        );
        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($content);

        $arrayContent = \json_decode($content, true);
        $this->assertCount(10, $arrayContent);
    }

    /*Test Add new Card*/
    /**
     * @group SuccesCard
     */
    public function testPostApiAdminAddCard()
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/cards/add', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '72312'
            ],
            '{
            "name": "NewCard",
            "creditCardType": "Visa",
            "creditCardNumber": 21474837,
            "currencyCode": "EUR",
            "value": 50000
            }'
        );

        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJson($content);
    }

    /*Test Fail Add new Card : value not correct*/
    /**
     * @group FailCard
     */
    public function testFailPostApiAdminAddCard()
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/cards/add', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '72312'
            ],
            '{
            "name": "NewCard",
            "creditCardType": "Visa",
            "creditCardNumber": 21474847,
            "currencyCode": "EUR",
            "value": 300000
            }'
        );

        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($content);
    }

    /*Test Fail Add new Card : blank*/
    /**
     * @group FailCard
     */
    public function testFailPostBlankApiAdminAddCard()
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/cards/add', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '72312'
            ],
            '{
            "name": "NewCard",
            "creditCardType": "",
            "creditCardNumber": ,
            "currencyCode": "EUR",
            "value": 3000
            }'
        );

        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($content);
    }

    /*Test Patch Card*/
    /**
     * @group SuccesCard
     */
    public function testPatchApiAdminAddCard()
    {
        $client = static::createClient();
        $client->request('PATCH', '/api/admin/cards/8', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '72312'
            ],
            '{
            "name": "PatchCard",
            "creditCardType": "Visa",
            "creditCardNumber": 11874847,
            "currencyCode": "EUR",
            "value": 80000,
            "user": {
            "id": 5
            }}'
        );

        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($content);
    }


    /**
     * @group SuccesCard
     */
    public function testDeleteApiCard()
    {
        $client = static::createClient();
        $client->request('Delete', '/api/admin/card/remove/9', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '72312'
            ]
        );

        $response = $client->getResponse();
        $this->assertEquals(204, $response->getStatusCode());
    }

    /*Test One Card User*/
    /**
     * @group SuccesCard
     */
    public function testGetApiUserOneCard()
    {
        $client = static::createClient();
        $client->request('GET', '/api/user/cards/6', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '99445'
            ]
        );

        $response = $client->getResponse();
        $content = $response->getContent();

        $card = \json_decode($content, true);

        $this->assertArrayHasKey('id', $card);
        $this->assertArrayHasKey('name', $card);
        $this->assertArrayHasKey('creditCardType', $card);
        $this->assertArrayHasKey('creditCardNumber', $card);
        $this->assertArrayHasKey('currencyCode', $card);
        $this->assertArrayHasKey('value', $card);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /*Test Fail One Card User*/
    /**
     * @group FailCard
     */
    public function testFailGetApiUserOneCard()
    {
        $client = static::createClient();
        $client->request('GET', '/api/user/cards/10', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '93324'
            ]
        );

        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }

    /*Test All Card User*/
    /**
     * @group SuccesCard
     */
    public function testGetApiUserAllCard()
    {
        $client = static::createClient();
        $client->request('GET', '/api/user/cards', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '99445'
            ]
        );
        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($content);

        $arrayContent = \json_decode($content, true);
        $this->assertCount(1, $arrayContent);
    }

    /*Test Add new Card for User*/
    /**
     * @group SuccesCard
     */
    public function testPostApiUserAddCard()
    {
        $client = static::createClient();
        $client->request('POST', '/api/user/cards/add', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '93324'
            ],
            '{
            "name": "UserCard",
            "creditCardType": "MasterCard",
            "creditCardNumber": 21474566,
            "currencyCode": "EUR",
            "value": 60000
            }'
        );

        $response = $client->getResponse();
        $content = $response->getContent();

        $card = \json_decode($content, true);

        $this->assertArrayHasKey('id', $card);
        $this->assertArrayHasKey('name', $card);
        $this->assertArrayHasKey('creditCardType', $card);
        $this->assertArrayHasKey('creditCardNumber', $card);
        $this->assertArrayHasKey('currencyCode', $card);
        $this->assertArrayHasKey('value', $card);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJson($content);
    }

    /*Test Fail Add new Card for User: blank assert*/
    /**
     * @group FailCard
     */
    public function testFailPostApiUserAddCard()
    {
        $client = static::createClient();
        $client->request('POST', '/api/user/cards/add', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '93324'
            ],
            '{
            "name": "",
            "creditCardType": "",
            "creditCardNumber": 21474566,
            "currencyCode": "EUR",
            "value": 60000
            "user": {
            "id": 2
            }
            }'
        );

        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }


    /*Test Fail Add new Card : value not correct*/
    /**
     * @group FailCard
     */
    public function testFailValuePostApiUserAddCard()
    {
        $client = static::createClient();
        $client->request('POST', '/api/user/cards/add', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '93324'
            ],
            '{
            "name": "NewCard",
            "creditCardType": "Visa",
            "creditCardNumber": 21474877,
            "currencyCode": "EUR",
            "value": 300000
            }'
        );

        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($content);
    }

    /*Test Patch User Card*/
    /**
     * @group SuccesCard
     */
    public function testPatchApiUserAddCard()
    {
        $client = static::createClient();
        $client->request('PATCH', '/api/user/cards/8', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '93324'
            ],
            '{
            "name": "PatchCardUser",
            "creditCardType": "Visa",
            "creditCardNumber": 1197483647,
            "currencyCode": "EUR",
            "value": 90000
            }'
        );

        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($content);
    }
}