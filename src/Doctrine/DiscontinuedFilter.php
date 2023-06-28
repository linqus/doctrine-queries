<?php

namespace App\Doctrine;

use App\Entity\FortuneCookie;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class DiscontinuedFilter extends SQLFilter 
{

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if ($targetEntity->name !== FortuneCookie::class) {
            return '';
        }


        return $targetTableAlias . ".discontinued = " . $this->getParameter('discontinued');

        dd($targetEntity);
    }
    

}