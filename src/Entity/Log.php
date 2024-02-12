<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogRepository")
 */
class Log
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UtmUser", inversedBy="logs")
     */
    private $utmUser;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserAliker", inversedBy="logs")
     */
    private $UserAliker;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $action;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtmUser(): ?UtmUser
    {
        return $this->utmUser;
    }

    public function setUtmUser(?UtmUser $utmUser): self
    {
        $this->utmUser = $utmUser;

        return $this;
    }

    public function getUserAliker(): ?UserAliker
    {
        return $this->UserAliker;
    }

    public function setUserAliker(?UserAliker $UserAliker): self
    {
        $this->UserAliker = $UserAliker;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(?\DateTimeInterface $dateTime): self
    {
        $this->dateTime = $dateTime;

        return $this;
    }
}
