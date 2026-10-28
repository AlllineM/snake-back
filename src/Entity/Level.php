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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $user_name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $difficulty;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rating;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $targetScore;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $allotedTime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $author_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $lifeMaxCount;

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

    public function getUserName(): ?string
    {
        return $this->user_name;
    }

    public function setUserName(?string $user_name): self
    {
        $this->user_name = $user_name;

        return $this;
    }

    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }

    public function setDifficulty(?int $difficulty): self
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getTargetScore(): ?int
    {
        return $this->targetScore;
    }

    public function setTargetScore(?int $targetScore): self
    {
        $this->targetScore = $targetScore;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAllotedTime(): ?int
    {
        return $this->allotedTime;
    }

    public function setAllotedTime(?int $allotedTime): self
    {
        $this->allotedTime = $allotedTime;

        return $this;
    }

    public function getAuthorId(): ?int
    {
        return $this->author_id;
    }

    public function setAuthorId(?int $author_id): self
    {
        $this->author_id = $author_id;

        return $this;
    }

    public function getLifeMaxCount(): ?int
    {
        return $this->lifeMaxCount;
    }

    public function setLifeMaxCount(?int $lifeMaxCount): self
    {
        $this->lifeMaxCount = $lifeMaxCount;

        return $this;
    }
}
