<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UtmUserRepository")
 */
class UtmUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserAliker", inversedBy="utmUsers")
     */
    private $userAliker;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $utmSource;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $utmMedium;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $utmCampaign;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $utmTerm;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $utmContent;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createDateTime;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Log", mappedBy="utmUser")
     */
    private $logs;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $temporaryId;

    public function __construct()
    {
        $this->logs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUtmSource(): ?string
    {
        return $this->utmSource;
    }

    public function setUtmSource(?string $utmSource): self
    {
        $this->utmSource = $utmSource;

        return $this;
    }

    public function getUtmMedium(): ?string
    {
        return $this->utmMedium;
    }

    public function setUtmMedium(?string $utmMedium): self
    {
        $this->utmMedium = $utmMedium;

        return $this;
    }

    public function getUtmCampaign(): ?string
    {
        return $this->utmCampaign;
    }

    public function setUtmCampaign(?string $utmCampaign): self
    {
        $this->utmCampaign = $utmCampaign;

        return $this;
    }

    public function getUtmTerm(): ?string
    {
        return $this->utmTerm;
    }

    public function setUtmTerm(?string $utmTerm): self
    {
        $this->utmTerm = $utmTerm;

        return $this;
    }

    public function getUtmContent(): ?string
    {
        return $this->utmContent;
    }

    public function setUtmContent(?string $utmContent): self
    {
        $this->utmContent = $utmContent;

        return $this;
    }

    public function getCreateDateTime(): ?\DateTimeInterface
    {
        return $this->createDateTime;
    }

    public function setCreateDateTime(?\DateTimeInterface $createDateTime): self
    {
        $this->createDateTime = $createDateTime;

        return $this;
    }

    /**
     * @return Collection|Log[]
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(Log $log): self
    {
        if (!$this->logs->contains($log)) {
            $this->logs[] = $log;
            $log->setUtmUser($this);
        }

        return $this;
    }

    public function removeLog(Log $log): self
    {
        if ($this->logs->contains($log)) {
            $this->logs->removeElement($log);
            // set the owning side to null (unless already changed)
            if ($log->getUtmUser() === $this) {
                $log->setUtmUser(null);
            }
        }

        return $this;
    }

    public function getTemporaryId(): ?string
    {
        return $this->temporaryId;
    }

    public function setTemporaryId(?string $temporaryId): self
    {
        $this->temporaryId = $temporaryId;

        return $this;
    }
}
