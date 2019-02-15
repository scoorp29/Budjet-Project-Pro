<?php

namespace App\Controller;

use App\Entity\User;
use App\Manager\CardManager;
use App\Manager\SubscriptionManager;
use App\Manager\UserManager;
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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class AdminController extends AbstractFOSRestController
{
    private $userManager;
    private $subscriptionManager;
    private $cardManager;
    private $validatorManager;
    private $em;

    public function __construct(UserManager $userManager, SubscriptionManager $subscriptionManager, CardManager $cardManager, ValidatorManager $validatorManager, EntityManagerInterface $em)
    {
        $this->userManager = $userManager;
        $this->subscriptionManager = $subscriptionManager;
        $this->cardManager = $cardManager;
        $this->validatorManager = $validatorManager;
        $this->em = $em;
    }

    /**
     * @Rest\Get("/api/admin/profile")
     * @Rest\View(serializerGroups={"user", "subscription", "card"})
     * @Security(name="api_key")
     * @SWG\Get(
     *      tags={"Admin/Admin"},
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
    public function getApiAdminProfile()
    {
        $user = $this->getUser();

        return $this->view($user, 200);
    }

    /**
     * @Rest\Get("/api/admin/users/profile/{id}")
     * @Rest\View(serializerGroups={"user", "subscription", "card"})
     * @Security(name="api_key"),
     * @SWG\Get(
     *      tags={"Admin/Users"},
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
    public function getApiUserProfile(User $user)
    {
        return $this->view($user, 200);
    }

    //List of all users

    /**
     * @Rest\Get("/api/admin/users")
     * @Rest\View(serializerGroups={"user", "subscription", "card"})
     * @Security(name="api_key")
     * @SWG\Get(
     *      tags={"Admin/Users"},
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
    public function getApiAllUsers()
    {
        $users = $this->userManager->findAll();

        return $this->view($users, 200);
    }

    /**
     * @Rest\Post("/api/admin/users/add")
     * @ParamConverter("user", converter="fos_rest.request_body")
     * @Rest\View(serializerGroups={"user", "subscription", "card"})
     * @Security(name="api_key")
     * @SWG\Post(
     *      tags={"Admin/Users"},
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
    public function postApiAdmiAddUser(User $user, Request $request, ConstraintViolationListInterface $validationErrors)
    {
        $role = ["ROLE_USER"];

        $subscription = $request->get('subscription');

        //Call UserManager function to check User Role;
        $verifyRole = $this->userManager->verifyUserRole($user, $role);



        if ($verifyRole) {
            if ($subscription == null) {
                throw new BadRequestHttpException('User should have a subscription!', null, 400);
            }
        }

        //We test if all the conditions are fulfilled (Assert in Entity / User)
        //Return -> Throw a 400 Bad Request with all errors messages
        $this->validatorManager->validateMyPostAssert($validationErrors);

        try {
            $this->em->persist($user);
            $this->em->flush();
        } catch (BadRequestHttpException $e) {
            return $this->view($e, 400);
        }

        return $this->view($user, 201);
    }

    /**
     * @Rest\Patch("/api/admin/profile")
     * @Rest\View(serializerGroups={"user", "subscription", "card"})
     * @Security(name="api_key")
     * @SWG\Patch(
     *      tags={"Admin/Admin"},
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
    public function patchApiAdminProfile(Request $request, ValidatorManager $validatorManager, ValidatorInterface $validator)
    {
        $user = $this->getUser();

        $firstname = $request->get('firstname');
        $lastname = $request->get('lastname');

        //Admin can edit his email
        $email = $request->get('email');
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

        if (null !== $email) {
            $user->setEmail($email);
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

        //We test if all the conditions are fulfilled (Assert in Entity / User)
        //Return -> Throw a 400 Bad Request with all errors messages
        $validatorManager->validateMyPatchAssert($user, $validator);

        $this->em->persist($user);
        $this->em->flush();

        return $this->view($user, 200);
    }

    /**
     * @Rest\Patch("/api/admin/users/profile/{id}")
     * @Rest\View(serializerGroups={"user", "subscription", "card"})
     * @Security(name="api_key")
     * @SWG\Patch(
     *      tags={"Admin/Users"},
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
    public function patchApiAdminUserProfile(User $user, Request $request, ValidatorInterface $validator)
    {
        $firstname = $request->get('firstname');
        $lastname = $request->get('lastname');

        //Admin can edit user email
        $email = $request->get('email');
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

        if (null !== $email) {
            $user->setEmail($email);
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

        //We test if all the conditions are fulfilled (Assert in Entity / User)
        //Return -> Throw a 400 Bad Request with all errors messages
        $this->validatorManager->validateMyPatchAssert($user, $validator);

        $this->em->persist($user);
        $this->em->flush();

        return $this->view($user, 200);
    }

    //Delete User and his cards

    /**
     * @Rest\Delete("/api/admin/users/profile/remove/{id}")
     * @Security(name="api_key")
     * @SWG\Delete(
     *      tags={"Admin/Users"},
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
    public function deleteApiUser(User $user)
    {
        $message = 'User delete successfully !';
        $cards = $this->cardManager->findBy(['user' => $user]);

        foreach ($cards as $card) {
            $card->setUser(null);
        }

        $this->em->remove($user);
        $this->em->flush();

        return $this->view($message, 204);
    }
}