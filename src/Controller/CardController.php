<?php

namespace App\Controller;

use App\Entity\Card;
use App\Manager\CardManager;
use App\Manager\UserManager;
use App\Manager\ValidatorManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CardController extends AbstractFOSRestController
{
    private $userManager;
    private $cardManager;
    private $validatorManager;
    private $em;

    public function __construct(UserManager $userManager, CardManager $cardManager, ValidatorManager $validatorManager, EntityManagerInterface $em)
    {
        $this->userManager = $userManager;
        $this->cardManager = $cardManager;
        $this->validatorManager = $validatorManager;
        $this->em = $em;
    }

    //One card

    /**
     * @Rest\Get("/api/admin/cards/{id}")
     * @Rest\View(serializerGroups={"card"})
     * @Security(name="api_key")
     * @SWG\Get(
     *      tags={"Card/Admin"},
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
    public function getApiAdminOneCard($id)
    {
        $card = $this->cardManager->find($id);

        return $this->view($card, 200);
    }

    //All Admin card

    /**
     * @Rest\Get("/api/admin/my/cards")
     * @Rest\View(serializerGroups={"card"})
     * @Security(name="api_key")
     * @SWG\Get(
     *      tags={"Card/User"},
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
    public function getApiAllAdminCards()
    {
        $user = $this->getUser();
        $cards = $this->cardManager->findBy(['user' => $user]);

        return $this->view($cards, 200);
    }


    //All card

    /**
     * @Rest\Get("/api/admin/cards")
     * @Rest\View(serializerGroups={"card"})
     * @Security(name="api_key")
     * @SWG\Get(
     *      tags={"Card/Admin"},
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
    public function getApiAdminAllCards()
    {
        $cards = $this->cardManager->findAll();

        return $this->view($cards, 200);
    }

    //Create One card by admin

    /**
     * @Rest\Post("/api/admin/cards/add")
     * @ParamConverter("card", converter="fos_rest.request_body")
     * @Rest\View(serializerGroups={"card"})
     * @Security(name="api_key")
     * @SWG\Post(
     *      tags={"Card/Admin"},
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
    public function postApiAdminCard(Card $card, ConstraintViolationListInterface $validationErrors)
    {
        //We test if all the conditions are fulfilled (Assert in Entity / Subscription)
        //Return -> Throw a 400 Bad Request with all errors messages
        $this->validatorManager->validateMyPostAssert($validationErrors);

        $this->em->persist($card);
        $this->em->flush();

        return $this->view($card, 201);
    }

    /**
     * @Rest\Patch("/api/admin/cards/{id}")
     * @Rest\View(serializerGroups={"card"})
     * @Security(name="api_key")
     * @SWG\Patch(
     *      tags={"Card/Admin"},
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
    public function patchApiAdminCard(Card $card, Request $request, ValidatorInterface $validator)
    {
        $name = $request->get('name');
        $creditCardType = $request->get('creditCardType');
        $creditCardNumber = $request->get('creditCardNumber');
        $currencyCode = $request->get('currencyCode');
        $value = $request->get('value');

        //Find user with id
        $user_id = $request->get('user');

        if (null !== $user_id) {
            $user = $this->userManager->find($user_id);
        } else {
            $user = null;
        }

        if (null !== $name) {
            $card->setName($name);
        }

        if (null !== $creditCardType) {
            $card->setCreditCardType($creditCardType);
        }

        if (null !== $creditCardNumber) {
            $card->setCreditCardNumber($creditCardNumber);
        }

        if (null !== $currencyCode) {
            $card->setCurrencyCode($currencyCode);
        }

        if (null !== $value) {
            $card->setValue($value);
        }

        if (null !== $user) {
            $card->setUser($user);
        }

        //We test if all the conditions are fulfilled (Assert in Entity / User)
        //Return -> Throw a 400 Bad Request with all errors messages
        $this->validatorManager->validateMyPatchAssert($card, $validator);

        $this->em->persist($card);
        $this->em->flush();

        return $this->view($card, 200);
    }

    //Delete one card by admin

    /**
     * @Rest\Delete("/api/admin/card/remove/{id}")
     * @Security(name="api_key")
     * @SWG\Delete(
     *      tags={"Card/Admin"},
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
    public function deleteApiCard(Card $card)
    {
        $message = 'Card are successfully removed !';

        $this->em->remove($card);
        $this->em->flush();

        return $this->view($message, 204);
    }

    //Get User Card by id (card)

    /**
     * @Rest\Get("/api/user/cards/{id}")
     * @Rest\View(serializerGroups={"card"})
     * @Security(name="api_key")
     * @SWG\Get(
     *      tags={"Card/User"},
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
    public function getApiUserOneCard(Card $card)
    {
        $user = $this->getUser();
        $userCard = $card->getUser();

        //A user can see the credit cards from other users
        if ($user !== $userCard) {
            throw new BadRequestHttpException('You don\'t have acces to see this card !', null, 400);
        }

        try {
            return $this->view($card, 200);
        } catch (BadRequestHttpException $e) {
            return $this->view($e, 400);
        }
    }

    //All User card

    /**
     * @Rest\Get("/api/user/cards")
     * @Rest\View(serializerGroups={"card"})
     * @Security(name="api_key")
     * @SWG\Get(
     *      tags={"Card/User"},
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
    public function getApiusersAllCards()
    {
        $user = $this->getUser();
        $cards = $this->cardManager->findBy(['user' => $user]);

        return $this->view($cards, 200);
    }

    //Create One card by User

    /**
     * @Rest\Post("/api/user/cards/add")
     * @ParamConverter("card", converter="fos_rest.request_body")
     * @Rest\View(serializerGroups={"card"})
     * @Security(name="api_key")
     * @SWG\Post(
     *      tags={"Card/User"},
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
    public function postApiUserCard(Card $card, Request $request, ConstraintViolationListInterface $validationErrors)
    {
        $user = $this->getUser();
        $userGive = $request->get('user');

        //A user can't decide who to assign the credit card to.
        if (null !== $userGive) {
            throw new BadRequestHttpException('You can\'t do this !', null, 400);
        }

        //We test if all the conditions are fulfilled (Assert in Entity / Subscription)
        //Return -> Throw a 400 Bad Request with all errors messages
        $this->validatorManager->validateMyPostAssert($validationErrors);

        try {
            //Set current user in Card
            $card->setUser($user);

            $this->em->persist($card);
            $this->em->flush();
        } catch (BadRequestHttpException $e) {
            return $this->view($e, 400);
        }

        return $this->view($card, 201);
    }

    //User edit his card
    /**
     * @Rest\Patch("/api/user/cards/{id}")
     * @Rest\View(serializerGroups={"card"})
     * @Security(name="api_key")
     * @SWG\Patch(
     *      tags={"Card/User"},
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
    public function patchApiUserCard(Card $card, Request $request, ValidatorInterface $validator)
    {
        $user = $this->getUser();

        $name = $request->get('name');
        $creditCardType = $request->get('creditCardType');
        $creditCardNumber = $request->get('creditCardNumber');
        $currencyCode = $request->get('currencyCode');
        $value = $request->get('value');
        $userGive = $request->get('user');

        if (null !== $name) {
            $card->setName($name);
        }

        if (null !== $creditCardType) {
            $card->setCreditCardType($creditCardType);
        }

        if (null !== $creditCardNumber) {
            $card->setCreditCardNumber($creditCardNumber);
        }

        if (null !== $currencyCode) {
            $card->setCurrencyCode($currencyCode);
        }

        if (null !== $value) {
            $card->setValue($value);
        }

        if (null !== $user) {
            $card->setUser($user);
        }

        //A user can't decide who to assign the credit card to.
        if (null !== $userGive) {
            throw new BadRequestHttpException('You can\'t do this !', null, 400);
        }

        //We test if all the conditions are fulfilled (Assert in Entity / User)
        //Return -> Throw a 400 Bad Request with all errors messages
        $this->validatorManager->validateMyPatchAssert($card, $validator);

        try {
            $this->em->persist($card);
            $this->em->flush();
        } catch (BadRequestHttpException $e) {
            return $this->view($e, 400);
        }

        return $this->view($card, 200);
    }

    //User delete hes cart

    /**
     * @Rest\Delete("/api/user/card/remove/{id}")
     * @Security(name="api_key")
     * @SWG\Delete(
     *      tags={"Card/User"},
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
    public function deleteApiUserCard(Card $card)
    {
        $message = 'Card are successfully removed !';
        $user = $this->getUser();
        $userCard = $card->getUser();

        //A user can't delete credit cards from other users
        if ($user !== $userCard) {
            throw new BadRequestHttpException('You don\'t have acces to remove this card !', null, 400);
        }

        try {
            $this->em->remove($card);
            $this->em->flush();
        } catch (BadRequestHttpException $e) {
            return $this->view($e, 400);
        }

        return $this->view($message, 204);
    }


}
