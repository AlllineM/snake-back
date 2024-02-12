<?php

namespace App\Services;

use App\Entity\Fact;
use App\Entity\Reaction;
use App\Repository\FactRepository;
use App\Repository\ReactionRepository;

class ReactionFactory
{
    private $factRepository;
    public function __construct(FactRepository $factRepository)
    {
        $this->factRepository = $factRepository;
    }

    public function makeReaction($user, $factId, $status)
    {
        $status = ReactionRepository::getStatus($status);

        $fact = $this->factRepository
            ->findOneById($factId);

        $reaction = new Reaction();
        $reaction->setUserAliker($user);
        $reaction->setFact($fact);
        $reaction->setStatus($status);
        $reaction->setDateTime(new \DateTime());

        return $reaction;
    }
}
