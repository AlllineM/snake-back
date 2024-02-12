<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LevelRepository")
 */
class Level
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
    private $user_token;

    /**
     * @ORM\Column(type="json")
     */
    private $blocks = [];

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
}
