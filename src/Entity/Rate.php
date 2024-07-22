<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RateRepository")
 */
class Rate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserRate", inversedBy="authorRates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserRate", inversedBy="subjectRates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subject;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?UserRate
    {
        return $this->author;
    }

    public function setAuthor(?UserRate $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getSubject(): ?UserRate
    {
        return $this->subject;
    }

    public function setSubject(?UserRate $subject): self
    {
        $this->subject = $subject;

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
