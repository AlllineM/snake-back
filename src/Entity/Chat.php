<?php

namespace App\Entity;

use App\Repository\RequestRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChatRepository::class)
 */
class Chat
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=UserAliker::class, inversedBy="chatsFrom")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userFrom;

    /**
     * @ORM\ManyToOne(targetEntity=UserAliker::class, inversedBy="chatsTo")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userTo;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetime;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserFrom(): ?UserAliker
    {
        return $this->userFrom;
    }

    public function setUserFrom(?UserAliker $userFrom): self
    {
        $this->userFrom = $userFrom;

        return $this;
    }

    public function getUserTo(): ?UserAliker
    {
        return $this->userTo;
    }

    public function setUserTo(?UserAliker $userTo): self
    {
        $this->userTo = $userTo;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }
}
