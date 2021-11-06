<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    const NEW = 0;
    const READ = 1;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=UserAliker::class, inversedBy="messagesFrom")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userFrom;

    /**
     * @ORM\ManyToOne(targetEntity=UserAliker::class, inversedBy="messagesTo")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userTo;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetime;

    /**
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;


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

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }
}
