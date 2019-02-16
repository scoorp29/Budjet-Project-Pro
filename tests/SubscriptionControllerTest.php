<?php
/**
 * Created by PhpStorm.
 * User: Ecole-IPPSI
 * Date: 01/02/2019
 * Time: 10:50
 */

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class SubscriptionControllerTest extends WebTestCase
{
//    public static function setUpBeforeClass()
//    {
//        exec('php bin/console hautelook:fixtures:load --append');
//
//        parent::setUpBeforeClass();
//    }

    /*Get One Subscription*/
    /**
     * @group SuccesSubscription
     */
    public function testGetApiAdminOneSubscription()
    {
        $client = static::createClient();
        $client->request('GET', '/api/subscriptions/3', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
            ]
        );

        $response = $client->getResponse();
        $content = $response->getContent();

        $subscriptions = \json_decode($content, true);

        $this->assertArrayHasKey('id', $subscriptions);
        $this->assertArrayHasKey('name', $subscriptions);
        $this->assertArrayHasKey('slogan', $subscriptions);
        $this->assertArrayHasKey('url', $subscriptions);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /*Get All Subscription*/
    /**
     * @group SuccesSubscription
     */
    public function testGetApiAdminAllSubscription()
    {
        $client = static::createClient();
        $client->request('GET', '/api/subscriptions', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
            ]
        );
        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($content);

        $arrayContent = \json_decode($content, true);
        $this->assertCount(10, $arrayContent);
    }

    /*Add new Subscription by admin*/
    /**
     * @group SuccesSubscription
     */
    public function testPostApiAdminAddSubscription()
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/subscriptions/add', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '72312'
            ],
            '{
            "name": "NewSubscription",
            "slogan": "MySubscription",
            "url": "https://d2j8c2rj2f9b78.cloudfront.net/uploads/hero-images/The-Avett-Brothers-Concert-Bojangles.jpg"
            }'
        );

        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJson($content);
    }

    /*Fail and new Subscription by admin*/
    /**
     * @group FailSubscription
     */
    public function testPostFailApiAdminAddSubscription()
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/subscriptions/add', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '72312'
            ],
            '{
            "name": "",
            "slogan": ""
            }'
        );

        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($content);
    }

    /*Add new Subscription by admin*/
    /**
     * @group SuccesSubscription
     */
    public function testPostFailUrlApiAdminAddSubscription()
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/subscriptions/add', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '72312'
            ],
            '{
            "name": "NewSubscription",
            "slogan": "MySubscription",
            "url": "urlNonValid"
            }'
        );

        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($content);
    }

    /*Edit Subscription by admin*/
    /**
     * @group SuccesSubscription
     */
    public function testPatchApiAdminAddSubscription()
    {
        $client = static::createClient();
        $client->request('PATCH', '/api/admin/subscriptions/edit/5', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '72312'
            ],
            '{
            "name": "NewPatchSubscription",
            "slogan": "MyPatchSubscription",
            "url": "http://www.grooove.de/wp-content/uploads/2013/11/concert-crowd.jpg"
            }'
        );

        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($content);
    }

    /*Fail Edit Subscription by admin. Can't be blank.*/
    /**
     * @group FailSubscription
     */
    public function testPatchFailApiAdminAddSubscription()
    {
        $client = static::createClient();
        $client->request('PATCH', '/api/admin/subscriptions/edit/5', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '72312'
            ],
            '{
            "name": "",
            "slogan": ""
            }'
        );

        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($content);
    }

    /*Fail Edit Subscription by admin. Valide url.*/
    /**
     * @group FailSubscription
     */
    public function testPatchFailUrlApiAdminAddSubscription()
    {
        $client = static::createClient();
        $client->request('PATCH', '/api/admin/subscriptions/edit/5', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '72312'
            ],
            '{
            "url": "notUnUrl",
            }'
        );

        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($content);
    }

    /*Remove Subscription by admin*/
    /**
     * @group SuccesSubscription
     */
    public function testDeleteApiAdminSubscription()
    {
        $client = static::createClient();
        $client->request('Delete', '/api/admin/subscriptions/remove/9', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '72312'
            ]
        );

        $response = $client->getResponse();
        $this->assertEquals(204, $response->getStatusCode());
    }

    /*Fail Remove Subscription by admin*/
    /**
     * @group FailSubscription
     */
    public function testDeleteFailApiAdminSubscription()
    {
        $client = static::createClient();
        $client->request('Delete', '/api/admin/subscriptions/remove/4', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '72312'
            ]
        );

        $response = $client->getResponse();
        $this->assertEquals(500, $response->getStatusCode());
    }
}