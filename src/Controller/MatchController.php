<?php

namespace App\Controller;

use App\Entity\Category;
use App\Service\IdToEntity;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MatchController extends AbstractController
{

    public function __construct(IdToEntity $idToEntity){
        $this->idToEntity = $idToEntity;
    }
    #[Route('/match', name: 'match')]
    public function index(): Response
    {
        $user = [];
        $matchs = [];

        if ($this->getUser()){
            $user = $this->getUser();
            $client = new Client();

            $response = $client->request('GET', 'http://localhost:3000/matchs',
                [
                    'json' => ['userId'=> $user->getId()]
                ]);

            // On a la table de tous les matchs avec les id des users et ressources
            $matchsTableId = json_decode($response->getBody()->getContents()) ;

            // Conversion de chaque match en array d'entities qu'on met dans un tableau de matchs
            $matchsTableEntities = [];
            foreach ($matchsTableId as $match) {
                $matchEntities = $this->idToEntity->getIdToEntity($match);
                array_push($matchsTableEntities, $matchEntities);
            }

            return $this->render('match/index.html.twig', [
                'user' => $user,
                'controller_name' => 'MatchController',
                'matchs' => $matchsTableEntities,
            ]);
        }
        else{
            return $this->render('match/index.html.twig', [
                'user'=> $user,
                'controller_name' => 'MatchController',
            ]);
        }
    }

    /**
     * @Route ("/match/confirmed", name="match.confirmed")
     * @param Request $request
     * @return Response
     */
    public function confirmed(IdToEntity $idToEntity):Response
    {
        $user = [];
        $matchs_confirmed = [];

        if ($this->getUser()){
            $user = $this->getUser();
            $client = new Client();

            $response = $client->request('GET', 'http://localhost:3000/matchs',
                [
                    'json' => ['userId'=> $user->getId()]
                ]);

            // On a la table de tous les matchs avec les id des users et ressources
            $matchsTableId = json_decode($response->getBody()->getContents()) ;

            // Conversion de chaque match en array d'entities qu'on met dans un tableau de matchs
            $matchsTableEntities = [];
            foreach ($matchsTableId as $match) {
                $matchEntities = $idToEntity->getIdToEntity($match);
                array_push($matchsTableEntities, $matchEntities);
            }

            return $this->render('match/confirmed.html.twig', [
                'user' => $user,
//                'controller_name' => 'MatchController',
                'matchs' => $matchsTableEntities,
            ]);
        }
        else{
            return $this->render('match/confirmed.html.twig', [
                'user'=> $user,
//                'controller_name' => 'MatchController',
            ]);
        }

//        return $this->render('match/confirmed.html.twig');
    }



}
