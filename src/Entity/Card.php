<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CardRepository")
 */
class Card
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("card")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @Groups("card")
     * @Groups("cardlight")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @Groups("card")
     */
    private $creditCardType;

    /**
     * @ORM\Column(type="integer", length=191, unique=true)
     * @Assert\NotBlank
     * @Groups("card")
     */
    private $creditCardNumber;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Currency
     * @Groups("card")
     */
    private $currencyCode;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Range(
     *      min = 0,
     *      max = 100000,
     *      minMessage = "Value minimum : {{ limit }} !",
     *      maxMessage = "Value maximum : {{ limit }} !"
     * )
     * @Groups("card")
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="cards")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreditCardType(): ?string
    {
        return $this->creditCardType;
    }

    public function setCreditCardType(string $creditCardType): self
    {
        $this->creditCardType = $creditCardType;

        return $this;
    }

    public function getCreditCardNumber(): ?int
    {
        return $this->creditCardNumber;
    }

    public function setCreditCardNumber(int $creditCardNumber): self
    {
        $this->creditCardNumber = $creditCardNumber;

        return $this;
    }

    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): self
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
