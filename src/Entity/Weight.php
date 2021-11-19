<?php

namespace App\Entity;

use App\Repository\WeightRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WeightRepository::class)
 */
class Weight
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $reaction_one;

    /**
     * @ORM\Column(type="integer")
     */
    private $reaction_two;

    /**
     * @ORM\Column(type="integer")
     */
    private $weight;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReactionOne(): ?int
    {
        return $this->reaction_one;
    }

    public function setReactionOne(int $reaction_one): self
    {
        $this->reaction_one = $reaction_one;

        return $this;
    }

    public function getReactionTwo(): ?int
    {
        return $this->reaction_two;
    }

    public function setReactionTwo(int $reaction_two): self
    {
        $this->reaction_two = $reaction_two;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }
}
