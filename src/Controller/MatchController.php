<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\MatchEntity;
use App\Entity\User;
use App\Repository\MatchEntityRepository;
use App\Service\GetMID;
use App\Service\IdToEntity;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class MatchController extends AbstractController
{

    public function __construct(IdToEntity $idToEntity, MatchEntityRepository $matchEntityRepository, EntityManagerInterface $em, GetMID $mid){
        $this->idToEntity = $idToEntity;
        $this->matchRepository = $matchEntityRepository;
        $this->em = $em;
        $this->mid = $mid;
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
            dump($matchsTableId);

            $matchsTableIdProcessed = [];

            foreach ($matchsTableId as $match){
                $length = count($match);

                $nbValues = array_count_values($match);
                dump($nbValues);
                unset($nbValues[$user->getId()]);
                dump($nbValues);

                if (max($nbValues)<2){
                    dump("Ya une fois chaque noeud.");
                    for ($i = 4; $i < $length;$i) {
                        if ($match[$i] == $match[0]){
                            $matchCut = array_slice($match, 0, $i+1);
                            if (in_array($matchCut,$matchsTableIdProcessed)){
                                break;
                            }
                            else{
                                $matchsTableIdProcessed[] = $matchCut;
                                break;
                            }
                        }
                        $i = $i + 4;
                    }
                }

//                dump($matchsTableIdProcessed);
            }

            // Conversion de chaque match en array d'entities qu'on met dans un tableau de matchs
//            $matchsTableEntities = [];
            foreach ($matchsTableIdProcessed as $match) {
                dump($match);
                $matchEntities = $this->idToEntity->getIdToEntity($match);
//                array_push($matchsTableEntities, $matchEntities);
                $mid = $this->mid->getMID($match);
                dump($mid);

                // COndition si mid existe deja
                $length = count($match);

                $test = ($length-1)/4;
                dump($test);

                dump(count($this->matchRepository->findBy(['MID'=>$mid])));

                if ($this->matchRepository->findBy(['MID'=>$mid]) == null){
                    $uuid = Uuid::v4();
                    dump($length);
                    for ($i = 4 ; $i < $length+1; $i){

                        $newMatch = new MatchEntity();

                        if ($i < $length-1 ){
                            // Faire une table pour stocker le mid de chaque match par son uuid et l'historique des changements
                            $newMatch->setUuid($uuid);
                            $newMatch->setMID($mid);
                            $newMatch->setProposalInP($matchEntities[$i-3]);
                            $newMatch->setRessourceIn($matchEntities[$i-2]);
                            $newMatch->setProposalInU($matchEntities[$i-1]);
                            $newMatch->setUser($matchEntities[$i]);
                            $newMatch->setProposalOutU($matchEntities[$i+1]);
                            $newMatch->setRessourceOut($matchEntities[$i+2]);
                            $newMatch->setProposalOutR($matchEntities[$i+3]);
                            $i = $i + 4 ;
                        }
                        else{
                            $newMatch->setUuid($uuid);
                            $newMatch->setMID($mid);
                            $newMatch->setProposalInP($matchEntities[$i-3]);
                            $newMatch->setRessourceIn($matchEntities[$i-2]);
                            $newMatch->setProposalInU($matchEntities[$i-1]);
                            $newMatch->setUser($matchEntities[$i]);
                            $newMatch->setProposalOutU($matchEntities[1]);
                            $newMatch->setRessourceOut($matchEntities[2]);
                            $newMatch->setProposalOutR($matchEntities[3]);

                            $i = $i + 4 ;

                        }

                        $this->em->persist($newMatch);
                        $this->em->flush();

                        $this->addFlash('success', 'Nouveau match!');

                        dump($newMatch);

                    }
                }
            }

            if (isset($_POST['mid']) && isset($_POST['ans'])){
                $match=$this->matchRepository->findBy(['MID'=>$_POST['mid'], 'user'=>$user]);

                $match[0]->setState($_POST['ans']);

                $this->em->persist($match[0]);
                $this->em->flush();
            }

            $matchs_news=$this->matchRepository->findBy(['user'=>$user, 'state'=>null]);
            $matchs_accepted=$this->matchRepository->findBy(['user'=>$user, 'state'=>1]);
            $matchs_declined=$this->matchRepository->findBy(['user'=>$user, 'state'=>0]);
            $matchs_completed = [];
            foreach ($matchs_accepted as $ma){
                $a = count($this->matchRepository->findBy(['MID'=>$ma->getMID()]));
                dump($a);
                $b = count($this->matchRepository->findBy(['MID'=>$ma->getMID(), 'state'=>1]));
                dump($b);
                if ($a == $b){
                    $matchs_completed = array_merge($matchs_completed, [$ma]);
                }
            }

            dump($matchs_completed);
            dump($matchs_news);
            dump($matchs_accepted);
            dump($matchs_declined);

//            dump($matchs_user);
//
//            foreach ($matchs_user as $mu) {
//                if ($mu->getState()=null){
//
//                }
//            }


//            foreach ($matchsTableEntities as $matchTableEntities){

//                foreach ($matchTableEntities as $entity){

//                    $entityName = $this->em->getMetadataFactory()->getMetadataFor(get_class($entity))->getName();
//                    dump($entityName);
//                    if ($entityName == "App\Entity\User"){
//                        dump("user");
//                    }
//                }
//                dump($matchTableEntities);
//            }




//                $newMatch->setArrMatch($match);
//                $this->em->persist($newMatch);
//                $this->em->flush();

//                $recherche = $this->matchRepository->findOneBy(['arrMatch'=> $match, 'state'=>null ]);
//                dump($recherche);
//                $allMatchs = $this->matchRepository->findAll();
//                dump($allMatchs);

//                foreach ($allMatchs as $matchDB){
//                    if ($matchDB->getArrMatch()==$match){
//
//                    }
//                    if ($matchDB->getState()==null){
//
//                    }
//                    elseif ($matchDB->getState() == 1){
//
//                    }
//                    elseif ($matchDB->getState() == 2){
//
//                    }
//                    else{
//
//                    }
//                    dump($test->getArrMatch()[0]);
//                }

//                dump($test);
//
//                if ($recherche !== null){
//                    dump("ALors la");
//                    $newMatch = new MatchEntity();
//                    $newMatch->setArrMatch($match);
//
//                    $this->em->persist($newMatch);
//                    $this->em->flush();
//
//                    dump($newMatch->getArrMatch());

//                }


//                foreach ($this->matchRepository as $matchRepo) {
//                    dump($matchRepo);
////                    if ($match == $matchRepo->getArrMatch()){
////                        $match = new MatchEntity();
////
////                    }
//                    dump($matchRepo);
//                }

//            dump($matchsTableEntities);

//            if for match.arrMatch chauqe champ de matchArr ca n'hexisiste pas
//            alors on ajoute dans Match le match

//            foreach ($matchsTableEntities as $matchArr)
//            {
//                dump($matchArr);
//
//
//            }


            return $this->render('match/index.html.twig', [
                'user' => $user,
                'controller_name' => 'MatchController',
//                'matchs' => $matchsTableEntities,
                'matchs_news' => $matchs_news,
                'matchs_completed'=>$matchs_completed,
                'matchs_accepted' => $matchs_accepted,
                'matchs_declined' => $matchs_declined,

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
     * @Route ("/match/add", name="match.add")
     * @param Request $request
     * @return Response
     */
    public function add(MatchEntityRepository $matchEntityRepository)
    {
        if ($this->getUser()) {
            $user = $this->getUser();
            if (isset($_POST['mid']) && isset($_POST['ans'])){
                $match=$this->matchRepository->findBy(['MID'=>$_POST['mid'], 'user'=>$user]);

                $match[0]->setState($_POST['ans']);

                $this->em->persist($match[0]);
                $this->em->flush();

                dump($match[0]->getState());
//                dump($_POST['userId']);

                echo "treasure will be set if the form has been submitted (to TRUE, I believe)";
            }
        }

//        $user = [];
//        $matchs_confirmed = [];
//
//        if ($this->getUser()){
//            $user = $this->getUser();
//            $client = new Client();
//
//            $response = $client->request('GET', 'http://localhost:3000/matchs',
//                [
//                    'json' => ['userId'=> $user->getId()]
//                ]);
//
//            // On a la table de tous les matchs avec les id des users et ressources
//            $matchsTableId = json_decode($response->getBody()->getContents()) ;
//
//            // Conversion de chaque match en array d'entities qu'on met dans un tableau de matchs
//            $matchsTableEntities = [];
//            foreach ($matchsTableId as $match) {
//                $matchEntities = $idToEntity->getIdToEntity($match);
//                array_push($matchsTableEntities, $matchEntities);
//            }
//
//            return $this->render('match/confirmed.html.twig', [
//                'user' => $user,
////                'controller_name' => 'MatchController',
//                'matchs' => $matchsTableEntities,
//            ]);
//        }
//        else{
//            return $this->render('match/confirmed.html.twig', [
//                'user'=> $user,
////                'controller_name' => 'MatchController',
//            ]);
//        }

        return $this->render('match/index.html.twig');
    }



}
