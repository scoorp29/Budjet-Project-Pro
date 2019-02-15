<?php

namespace App\Controller;

use App\Entity\User;
use App\Manager\UserManager;
use App\Manager\ValidatorManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AnonymousController extends AbstractFOSRestController
{
    private $userManager;
    private $validatorManager;
    private $em;

    public function __construct(UserManager $userManager, ValidatorManager $validatorManager, EntityManagerInterface $em)
    {
        $this->userManager = $userManager;
        $this->validatorManager = $validatorManager;
        $this->em = $em;
    }

    //List of all users

    /**
     * @Rest\Get("/api/all-users")
     * @Rest\View(serializerGroups={"userlight", "cardlight", "subscription"})
     * @SWG\Get(
     *      tags={"Anonymous"},
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

    //One user

    /**
     * @Rest\Get("/api/profile/{id}")
     * @Rest\View(serializerGroups={"userlight", "subscription"})
     * @SWG\Get(
     *     tags={"Anonymous"},
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
     *     )
     */
    public function getApiUserProfile(User $user)
    {
        return $this->view($user, 200);
    }

    //Register new user

    /**
     * @Rest\Post("/api/new/user")
     * @ParamConverter("user", converter="fos_rest.request_body")
     * @Rest\View(serializerGroups={"user"})
     * @SWG\Post(
     *     tags={"Anonymous"},
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
     *     )
     */
    public function postApiNewUser(User $user, Request $request, ConstraintViolationListInterface $validationErrors)
    {
        $role = ["ROLE_USER"];

        $subscription = $request->get('subscription');

        //Call UserManager function to check User Role;
        $verifyRole = $this->userManager->verifyUserRole($user, $role);

        //We test if all the conditions are fulfilled (Assert in Entity / User)
        //Return -> Throw a Bad Request with all errors messages
        $this->validatorManager->validateMyPostAssert($validationErrors);


        //New exception if user don't send a subscription
        if ($verifyRole) {
            if ($subscription == null) {
                throw new BadRequestHttpException('You didn\'t choice your subscription!', null, 400);
            }
        }

        try {
            $this->em->persist($user);
            $this->em->flush();
        } catch (BadRequestHttpException $e) {
            return $this->view($e, 400);
        }

        return $this->view($user, 201);
    }
}
