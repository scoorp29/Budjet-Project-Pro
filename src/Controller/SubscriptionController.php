<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Manager\SubscriptionManager;
use App\Manager\ValidatorManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class SubscriptionController extends AbstractFOSRestController
{
    private $subscriptionManager;
    private $validatorManager;
    private $em;

    public function __construct(SubscriptionManager $subscriptionManager, ValidatorManager $validatorManager, EntityManagerInterface $em)
    {
        $this->subscriptionManager = $subscriptionManager;
        $this->validatorManager = $validatorManager;
        $this->em = $em;
    }

    //All subscriptions

    /**
     * @Rest\Get("/api/subscriptions")
     * @Rest\View(serializerGroups={"subscription"})
     * @SWG\Get(
     *      tags={"Subscription"},
     *      @SWG\Response(
     *             response=200,
     *             description="Success",
     *         ),
     *     @SWG\Response(
     *             response=204,
     *             description="No Content",
     *         ),
     *      @SWG\Response(
     *             response=400,
     *             description="Bad Request",
     *         ),
     *      @SWG\Response(
     *             response=403,
     *             description="Forbiden",
     *         ),
     *      @SWG\Response(
     *             response=404,
     *             description="Not Found",
     *         ),
     *)
     */
    public function getApiAdminAllSubscription()
    {
        $subscription = $this->subscriptionManager->findAll();

        return $this->view($subscription, 200);
    }

    //One subscriptions

    /**
     * @Rest\Get("/api/subscriptions/{id}")
     * @Rest\View(serializerGroups={"subscription"})
     * @SWG\Get(
     *      tags={"Subscription"},
     *      @SWG\Response(
     *             response=200,
     *             description="Success",
     *         ),
     *     @SWG\Response(
     *             response=204,
     *             description="No Content",
     *         ),
     *      @SWG\Response(
     *             response=400,
     *             description="Bad Request",
     *         ),
     *      @SWG\Response(
     *             response=403,
     *             description="Forbiden",
     *         ),
     *      @SWG\Response(
     *             response=404,
     *             description="Not Found",
     *         ),
     *)
     */
    public function getApiAdminOneSubscription($id)
    {
        $subscription = $this->subscriptionManager->findOneBy(['id' => $id]);

        return $this->view($subscription, 200);
    }

    //Create One subscriptions by admin

    /**
     * @Rest\Post("/api/admin/subscriptions/add")
     * @ParamConverter("subscription", converter="fos_rest.request_body")
     * @Rest\View(serializerGroups={"subscription"})
     * @Security(name="api_key")
     * @SWG\Post(
     *      tags={"Subscription/Admin"},
     *      @SWG\Response(
     *             response=201,
     *             description="Created",
     *         ),
     *      @SWG\Response(
     *             response=400,
     *             description="Bad Request",
     *         ),
     *      @SWG\Response(
     *             response=403,
     *             description="Forbiden",
     *         ),
     *      @SWG\Response(
     *             response=404,
     *             description="Not Found",
     *         ),
     *)
     */
    public function postApiAdminSubscription(Subscription $subscription, ConstraintViolationListInterface $validationErrors)
    {
        //We test if all the conditions are fulfilled (Assert in Entity / Subscription)
        //Return -> Throw a 400 Bad Request with all errors messages
        $this->validatorManager->validateMyPostAssert($validationErrors);

        $this->em->persist($subscription);
        $this->em->flush();
        return $this->view($subscription, 201);
    }

    //Edit One subscriptions by admin

    /**
     * @Rest\Patch("/api/admin/subscriptions/edit/{id}")
     * @Rest\View(serializerGroups={"subscription"})
     * @Security(name="api_key")
     * @SWG\Patch(
     *      tags={"Subscription/Admin"},
     *      @SWG\Response(
     *             response=200,
     *             description="Success",
     *         ),
     *      @SWG\Response(
     *             response=400,
     *             description="Bad Request",
     *         ),
     *      @SWG\Response(
     *             response=403,
     *             description="Forbiden",
     *         ),
     *      @SWG\Response(
     *             response=404,
     *             description="Not Found",
     *         ),
     *)
     */
    public function patchApiAdminSubscription(ValidatorInterface $validator, Request $request, $id)
    {
        $subscription = $this->subscriptionManager->findOneBy(['id' => $id]);

        $name = $request->get('name');
        $slogan = $request->get('slogan');
        $url = $request->get('url');

        if (null !== $name) {
            $subscription->setName($name);
        }

        if (null !== $slogan) {
            $subscription->setSlogan($slogan);
        }

        if (null !== $url) {
            $subscription->setUrl($url);
        }

        //We test if all the conditions are fulfilled (Assert in Entity / Subscription)
        //Return -> Throw a 400 Bad Request with all errors messages
        $this->validatorManager->validateMyPatchAssert($subscription, $validator);

        $this->em->persist($subscription);
        $this->em->flush();
        return $this->view($subscription, 200);
    }

    //Delete one subscription by admin

    /**
     * @Rest\Delete("/api/admin/subscriptions/remove/{id}")
     * @Security(name="api_key")
     * @SWG\Delete(
     *      tags={"Subscription/Admin"},
     *     @SWG\Response(
     *             response=204,
     *             description="No Content",
     *         ),
     *      @SWG\Response(
     *             response=400,
     *             description="Bad Request",
     *         ),
     *      @SWG\Response(
     *             response=403,
     *             description="Forbiden",
     *         ),
     *      @SWG\Response(
     *             response=404,
     *             description="Not Found",
     *         ),
     *      @SWG\Response(
     *             response=500,
     *             description="Foreign Key Violation",
     *         ),
     *)
     */
    public function deleteApiSubscription($id)
    {
        $subscription = $this->subscriptionManager->findOneBy(['id' => $id]);

        $message = 'Subscription are successfully removed !';

        $this->em->remove($subscription);
        $this->em->flush();

        return $this->view($message, 204);
    }
}
