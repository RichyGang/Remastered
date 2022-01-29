<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;
use App\Service\IdToEntity;

class AcceuilController extends AbstractController
{

    #[Route('/', name: 'acceuil')]
    public function index(IdToEntity $idToEntity): Response
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
                $matchEntities = $idToEntity->getIdToEntity($match);
                array_push($matchsTableEntities, $matchEntities);
            }

//            if (){
//
//            }

            return $this->render('acceuil/index.html.twig', [
                'user' => $user,
                'controller_name' => 'AcceuilController',
                'matchs' => $matchsTableEntities,
            ]);
        }
        else{
        return $this->render('acceuil/index.html.twig', [
            'user'=> $user,
            'controller_name' => 'AcceuilController',
        ]);
        }

    }
}
//
//        dump(json_decode($response));

//        $response = file_get_contents('http://localhost:3000/matchs');
//
//        // Takes raw data from the request
//        $json = file_get_contents('http://localhost:3000/matchs');

        // Converts it into a PHP object
//        $data = json_decode($json);

//        dump($data);

//        ----------------POST

//        $client = new Client();
//
//        $ressourceTest = 'bar';
//
//        $response = $client->request('POST', 'http://localhost:3000/ressource/add',
//            [
//                'json'    => ['ressource' => $ressourceTest]
//            ]
//        );

//        echo $response->getStatusCode(); // 200

//        ------------------


