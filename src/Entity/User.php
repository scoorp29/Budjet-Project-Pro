<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    public function __construct()
    {
        $this->roles = array('ROLE_USER');
        $this->apiKey = mt_rand(1000, 100000);
        $this->createdAt = new \DateTime();
        $this->cards = new ArrayCollection();
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("user")
     */
    private $id;

    /**
     * @ORM\Column(type="simple_array")
     * @Assert\NotBlank
     * @Groups("user")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("user")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("user")
     * @Groups("userlight")
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=191, unique=true)
     * @Assert\NotBlank
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     * @Groups("user")
     * @Groups("userlight")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=191, unique=true)
     * @Groups("user")
     */
    private $apiKey;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime(
     *     message="The value {{ value }} is not a valid date."
     * )
     * @Groups("user")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("user")
     */
    private $adress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Country
     * @Groups("user")
     */
    private $country;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Subscription", inversedBy="users")
     * @ORM\JoinColumn(nullable=true)
     * @Groups("subscription")
     */
    private $subscription;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Card", mappedBy="user")
     * @Groups("card")
     * @Groups("cardlight")
     */
    private $cards;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(?string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getPassword()
    {
        // TODO: Implement getPassword() method.
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function setSubscription(?Subscription $subscription): self
    {
        if ($this->getRoles() === 'ROLE_USER') {
            exit();
        }

        $this->subscription = $subscription;
        return $this;
    }

    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @return Collection|Card[]
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(Card $card): self
    {
        if (!$this->cards->contains($card)) {
            $this->cards[] = $card;
            $card->setUser($this);
        }

        return $this;
    }

    public function removeCard(Card $card): self
    {
        if ($this->cards->contains($card)) {
            $this->cards->removeElement($card);
            // set the owning side to null (unless already changed)
            if ($card->getUser() === $this) {
                $card->setUser(null);
            }
        }

        return $this;
    }
}
