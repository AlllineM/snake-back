<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LevelRepository")
 */
class Level
{
    public const STATUS_NEW = 0;
    public const STATUS_MODERATION = 1;
    public const STATUS_PUBLISHED = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $user_token;

    /**
     * @ORM\Column(type="json")
     */
    private $blocks = [];

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $startSpeed;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $boostSpeed;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserToken(): ?string
    {
        return $this->user_token;
    }

    public function setUserToken(?string $user_token): self
    {
        $this->user_token = $user_token;

        return $this;
    }

    public function getBlocks(): ?array
    {
        return $this->blocks;
    }

    public function setBlocks(array $blocks): self
    {
        $this->blocks = $blocks;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStartSpeed(): ?int
    {
        return $this->startSpeed;
    }

    public function setStartSpeed(?int $startSpeed): self
    {
        $this->startSpeed = $startSpeed;

        return $this;
    }

    public function getBoostSpeed(): ?int
    {
        return $this->boostSpeed;
    }

    public function setBoostSpeed(?int $boostSpeed): self
    {
        $this->boostSpeed = $boostSpeed;

        return $this;
    }
}
