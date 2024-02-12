<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TelegramUserRepository")
 */
class TelegramUser
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserAliker", inversedBy="telegramUser", cascade={"persist", "remove"})
     */
    private $userAliker;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isPushAllowed;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUserAliker(): ?UserAliker
    {
        return $this->userAliker;
    }

    public function setUserAliker(?UserAliker $userAliker): self
    {
        $this->userAliker = $userAliker;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getIsPushAllowed(): ?bool
    {
        return $this->isPushAllowed;
    }

    public function setIsPushAllowed(?bool $isPushAllowed): self
    {
        $this->isPushAllowed = $isPushAllowed;

        return $this;
    }
}
