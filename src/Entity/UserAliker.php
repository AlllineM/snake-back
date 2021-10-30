<?php

namespace App\Entity;

use App\Repository\UserAlikerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserAlikerRepository::class)
 * @UniqueEntity(fields={"phone"}, message="Аккаунт с таким номером телефона уже зарегестрирован")
 */
class UserAliker implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nick;

    /**
     * @ORM\Column(type="integer")
     */
    private $balance;

    /**
     * @ORM\OneToMany(targetEntity=Fact::class, mappedBy="author")
     */
    private $facts;

    /**
     * @ORM\OneToMany(targetEntity=Reaction::class, mappedBy="userAliker")
     */
    private $reactions;

    /**
     * @ORM\Column(type="integer")
     */
    private $rating;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     */
    private $apiToken;

    /**
     * @ORM\OneToMany(targetEntity=Request::class, mappedBy="userFrom")
     */
    private $outgoingRequests;

    /**
     * @ORM\OneToMany(targetEntity=Request::class, mappedBy="userTo")
     */
    private $incomingRequests;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="userFrom")
     */
    private $messagesFrom;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="userTo")
     */
    private $messagesTo;


    public function __construct()
    {
        $this->facts = new ArrayCollection();
        $this->reactions = new ArrayCollection();
        $this->outgoingRequests = new ArrayCollection();
        $this->incomingRequests = new ArrayCollection();
        $this->messagesFrom = new ArrayCollection();
        $this->messagesTo = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->nick;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNick(): ?string
    {
        return $this->nick;
    }

    public function setNick(?string $nick): self
    {
        $this->nick = $nick;

        return $this;
    }

    public function getBalance(): ?int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return Collection|Fact[]
     */
    public function getFacts(): Collection
    {
        return $this->facts;
    }

    public function addFact(Fact $fact): self
    {
        if (!$this->facts->contains($fact)) {
            $this->facts[] = $fact;
            $fact->setAuthor($this);
        }

        return $this;
    }

    public function removeFact(Fact $fact): self
    {
        if ($this->facts->contains($fact)) {
            $this->facts->removeElement($fact);
            // set the owning side to null (unless already changed)
            if ($fact->getAuthor() === $this) {
                $fact->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Reaction[]
     */
    public function getReactions(): Collection
    {
        return $this->reactions;
    }

    public function addReaction(Reaction $reaction): self
    {
        if (!$this->reactions->contains($reaction)) {
            $this->reactions[] = $reaction;
            $reaction->setUserAliker($this);
        }

        return $this;
    }

    public function removeReaction(Reaction $reaction): self
    {
        if ($this->reactions->contains($reaction)) {
            $this->reactions->removeElement($reaction);
            // set the owning side to null (unless already changed)
            if ($reaction->getUserAliker() === $this) {
                $reaction->setUserAliker(null);
            }
        }

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(?string $apiToken): self
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * @return Collection|Request[]
     */
    public function getOutgoingRequests(): Collection
    {
        return $this->outgoingRequests;
    }

    public function addOutgoingRequest(Request $outgoingRequest): self
    {
        if (!$this->outgoingRequests->contains($outgoingRequest)) {
            $this->outgoingRequests[] = $outgoingRequest;
            $outgoingRequest->setUserFrom($this);
        }

        return $this;
    }

    public function removeOutgoingRequest(Request $outgoingRequest): self
    {
        if ($this->outgoingRequests->contains($outgoingRequest)) {
            $this->outgoingRequests->removeElement($outgoingRequest);
            // set the owning side to null (unless already changed)
            if ($outgoingRequest->getUserFrom() === $this) {
                $outgoingRequest->setUserFrom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Request[]
     */
    public function getIncomingRequests(): Collection
    {
        return $this->incomingRequests;
    }

    public function addIncomingRequest(Request $incomingRequest): self
    {
        if (!$this->incomingRequests->contains($incomingRequest)) {
            $this->incomingRequests[] = $incomingRequest;
            $incomingRequest->setUserTo($this);
        }

        return $this;
    }

    public function removeIncomingRequest(Request $incomingRequest): self
    {
        if ($this->incomingRequests->contains($incomingRequest)) {
            $this->incomingRequests->removeElement($incomingRequest);
            // set the owning side to null (unless already changed)
            if ($incomingRequest->getUserTo() === $this) {
                $incomingRequest->setUserTo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessagesFrom(): Collection
    {
        return $this->messagesFrom;
    }

    public function addMessageFrom(Message $messageFrom): self
    {
        if (!$this->messagesFrom->contains($messageFrom)) {
            $this->messagesFrom[] = $messageFrom;
            $messageFrom->setUserFrom($this);
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessagesTo(): Collection
    {
        return $this->messagesTo;
    }

    public function addMessageTo(Message $messageTo): self
    {
        if (!$this->messagesTo->contains($messageTo)) {
            $this->messagesTo[] = $messageTo;
            $messageTo->setUserFrom($this);
        }

        return $this;
    }
}
