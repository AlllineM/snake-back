<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReactionRepository")
 */
class Reaction
{
    const AUTHOR = 'author';
    const SAME = 'same';
    const LIKE = 'like';
    const SKIP = 'skip';
    const DISLIKE = 'dislike';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Fact", inversedBy="reactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fact;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_time;

    /**
     * @ORM\Column
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=UserAliker::class, inversedBy="reactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userAliker;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFact(): ?Fact
    {
        return $this->fact;
    }

    public function setFact(?Fact $fact): self
    {
        $this->fact = $fact;

        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->date_time;
    }

    public function setDateTime(\DateTimeInterface $date_time): self
    {
        $this->date_time = $date_time;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus($status): self
    {
        $this->status = $status;

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
}
