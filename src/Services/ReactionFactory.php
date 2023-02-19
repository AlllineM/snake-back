<?php

namespace App\Services;

use App\Entity\Reaction;

class ReactionFactory
{
    public function makeReaction($user, $fact, $status)
    {
        $reaction = new Reaction();
        $reaction->setUserAliker($user);
        $reaction->setFact($fact);
        $reaction->setStatus($status);
        $reaction->setDateTime(new \DateTime());

        return $reaction;
    }
}
