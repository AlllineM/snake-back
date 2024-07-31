<?php

namespace App\Entity;

use App\Helpers\Logger;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Random\RandomException;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRateRepository")
 */
class UserRate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nick;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoLink;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $regDateTime;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Rate", mappedBy="author")
     */
    private $authorRates;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Rate", mappedBy="subject")
     */
    private $subjectRates;

    public function __construct()
    {
        $this->authorRates = new ArrayCollection();
        $this->subjectRates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(?string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getPhotoLink(): ?string
    {
        return $this->photoLink;
    }

    public function setPhotoLink(?string $photoLink): self
    {
        $this->photoLink = $photoLink;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getRegDateTime(): ?\DateTimeInterface
    {
        return $this->regDateTime;
    }

    public function setRegDateTime(?\DateTimeInterface $regDateTime): self
    {
        $this->regDateTime = $regDateTime;

        return $this;
    }

    /**
     * @return Collection|Rate[]
     */
    public function getAuthorRates(): Collection
    {
        return $this->authorRates;
    }

    public function addAuthorRate(Rate $authorRate): self
    {
        if (!$this->authorRates->contains($authorRate)) {
            $this->authorRates[] = $authorRate;
            $authorRate->setAuthor($this);
        }

        return $this;
    }

    public function removeAuthorRate(Rate $authorRate): self
    {
        if ($this->authorRates->contains($authorRate)) {
            $this->authorRates->removeElement($authorRate);
            // set the owning side to null (unless already changed)
            if ($authorRate->getAuthor() === $this) {
                $authorRate->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Rate[]
     */
    public function getSubjectRates(): Collection
    {
        return $this->subjectRates;
    }

    public function addSubjectRate(Rate $subjectRate): self
    {
        if (!$this->subjectRates->contains($subjectRate)) {
            $this->subjectRates[] = $subjectRate;
            $subjectRate->setSubject($this);
        }

        return $this;
    }

    public function removeSubjectRate(Rate $subjectRate): self
    {
        if ($this->subjectRates->contains($subjectRate)) {
            $this->subjectRates->removeElement($subjectRate);
            // set the owning side to null (unless already changed)
            if ($subjectRate->getSubject() === $this) {
                $subjectRate->setSubject(null);
            }
        }

        return $this;
    }

    /**
     * @throws RandomException
     */
    public function generateImageName(Logger $logger): string
    {
        $logger->errorTelegram("111");

        $randomHex = bin2hex(random_bytes(18));
        $logger->errorTelegram("222");

        $result = $this->id . '_' . $randomHex;

        $logger->errorTelegram("333");

        return $result;
    }

    public static function generateAnonImageName(): string
    {
        $randomHex = bin2hex(random_bytes(18));
        return 'anon' . '_' . $randomHex;
    }
}
