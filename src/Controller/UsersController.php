<?php

namespace App\Controller;

use App\Manager\SubscriptionManager;
use App\Manager\ValidatorManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UsersController extends AbstractFOSRestController
{
    private $validatorManager;
    private $subscriptionManager;
    private $em;

    public function __construct(ValidatorManager $validatorManager, SubscriptionManager $subscriptionManager, EntityManagerInterface $em)
    {
        $this->validatorManager = $validatorManager;
        $this->subscriptionManager = $subscriptionManager;
        $this->em = $em;
    }

    /**
     * @Rest\Get("/api/user/profile")
     * @Rest\View(serializerGroups={"user", "subscription", "card"})
     * @Security(name="api_key")
     * @SWG\Get(
     *     tags={"User"},
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
    public function getApiUserProfile()
    {
        $user = $this->getUser();
        return $this->view($user, 200);
    }

    /**
     * @Rest\Patch("/api/user/profile")
     * @Rest\View(serializerGroups={"user", "subscription", "card"})
     * @Security(name="api_key")
     * @SWG\Patch(
     *     tags={"User"},
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
    public function patchApiUserProfile(Request $request, ValidatorInterface $validator)
    {
        //We test if all the conditions are fulfilled (Assert in Entity / User)
        //Return false if not
        $user = $this->getUser();

        $firstname = $request->get('firstname');
        $lastname = $request->get('lastname');
        $adress = $request->get('adress');
        $country = $request->get('country');
        //Find subscription with id
        $subscription_id = $request->get('subscription');
        if (null !== $subscription_id) {
            $subscription = $this->subscriptionManager->find($subscription_id);
        } else {
            $subscription = null;
        }

        if (null !== $firstname) {
            $user->setFirstname($firstname);
        }

        if (null !== $lastname) {
            $user->setLastname($lastname);
        }

        if (null !== $adress) {
            $user->setAdress($adress);
        }

        if (null !== $country) {
            $user->setCountry($country);
        }

        if (null !== $subscription) {
            $user->setSubscription($subscription);
        }

        $this->validatorManager->validateMyPatchAssert($user, $validator);

        $this->em->persist($user);
        $this->em->flush();
        return $this->view($user, 200);
    }
}
