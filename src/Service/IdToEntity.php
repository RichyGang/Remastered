<?php

namespace App\Service;

use App\Repository\ProposalRepository;
use App\Repository\RessourceRepository;
use App\Repository\UserRepository;

class IdToEntity
{
    public function __construct(RessourceRepository $ressourceRepository, UserRepository $userRepository, ProposalRepository $proposalRepository){
        $this->userRepository = $userRepository;
        $this->ressourceRepository = $ressourceRepository;
        $this->proposalRepository = $proposalRepository;
    }

    function getIdToEntity($array):array
    {
        $entities = [];
        $i = 0;
        foreach ($array as $id){
//            user = 0 et 3 et 6
//            proposal = 1 et 4

            if ($i == 0){
                array_push($entities, $this->userRepository->findOneBy(['id'=>$id]));
                $i = $i + 1;
            }
            elseif ($i %2 != 0){
                array_push($entities, $this->proposalRepository->findOneBy(['id'=>$id]));
                $i = $i + 1;
            }
            elseif ($i %2 == 0 && $i %4 != 0){
                array_push($entities, $this->ressourceRepository->findOneBy(['id'=>$id]));
                $i = $i + 1;
            }
            elseif ($i %4 == 0){
                array_push($entities, $this->userRepository->findOneBy(['id'=>$id]));
                $i = $i + 1;
            }

        }

        return $entities;
    }
}