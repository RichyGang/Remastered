<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Proposal;
use App\Entity\User;
use App\Form\ProposalType;
use App\Repository\CategoryAttributeRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProposalRepository;
use App\Repository\RessourceAttributeRepository;
use App\Repository\RessourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\PathCategory;
use GuzzleHttp\Client;


class ProposalController extends AbstractController
{
//    /**
//     * @var ProposalRepository
//     */
//    private $proposalRepository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(ProposalRepository $proposalRepository, EntityManagerInterface $em)
    {
        $this->proposalRepository = $proposalRepository;
        $this->em = $em;

    }

    /**
     * @Route("/proposal/{categId}", name="proposal", requirements={"categId"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function index(ProposalRepository $proposalRepository, CategoryRepository $categoryRepository, RessourceRepository $ressourceRepository, Request $request, int $categId = 0): Response
    {
        if ($categId != 0){
            //        CONSTRUCTION DU CHEMIN DE LA CATEGORIE
            $category = $categoryRepository->find($categId);
            $pathCategory = [];
            while ($category->getMother() != null){
                array_push($pathCategory, $category->getMother()->getName());
                $category = $category->getMother();
            }
            $pathCategory = array_reverse($pathCategory);
            dump($pathCategory);
            //            END

            //         RÉCUPÉRATION DES TOUTES LES ATTRIBUTS DE CETTE CATÉGORIE
        }
        else{
            $category = null;
            $pathCategory = null;
        }

//        initialise car si pas encore selectionné de cat_mother alors ne peut pas le retourner
        $categoryChildren=null;



        /** @var User $user */
        $user = $this->getUser();

        $categoryMother = $categoryRepository->findBy(['mother'=>null]);

        if ($user) {
            $user_id = $user->getId();
//            $proposals = $proposalRepository->findAllExcept($user_id);
            $proposals = $proposalRepository->findAll();

            return $this->render('proposal/index.html.twig', [
                'category' => $category,
                'pathcategory'=> $pathCategory,
                'proposals' => $proposals,
                'categorymother' => $categoryMother,
                'categorychildren'=>$categoryChildren,
                'categories' => $categoryRepository->findAll()
            ]);
        }
        else {
            $proposals = $proposalRepository->findAll();

            return $this->render('proposal/index.html.twig', [
                'category' => $category,
                'pathcategory'=> $pathCategory,
                'proposals' => $proposals,
                'categorymother' => $categoryMother,
                'categorychildren'=>$categoryChildren,
                'categories' => $categoryRepository->findAll()
            ]);
        }

    }

    /**
     * @Route("/proposal/new/{ressId}", name="proposal.new", requirements={"ressId"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function new(PathCategory $pathCategory, CategoryRepository $categoryRepository, RessourceRepository $ressourceRepository, Request $request, int $ressId = 0): Response //le nom des variables doivent etre ceux des parametres de la route dans l'annotation | mais ici on a mis l'injection de la Ressource et donc ca fait le find tout seul
    {
        $categoryMother = $categoryRepository->findBy(['mother'=>null]);

        $user = $this->getUser();
        $proposal = new Proposal();

        $em = $this->getDoctrine()->getManager();

        // Pour proposer toutes les categories pour en choisir une
        $categories = $categoryRepository->findAll();

        // On recupere le nom de la categorie puis l'entité (surement un moyen de recup direct l'entité)
        $category_name = $request->query->get('category');
        $category = $categoryRepository->findOneBy(['name'=>$category_name]);

        $ressources = null;
        $path = null;

        if ($category != null){
            $ressources = $ressourceRepository->findBy(['category'=>$category]);
            $path= $pathCategory->getPathCategory($category);
        }

        if ($ressId != null)
        {
            $ressource = $ressourceRepository->findOneBy(['id'=>$ressId]);
        }
        else{
            //        on récupère l'id de la ressource sélectionné
            $ressource_id = $request->query->get('ressource_id');
            //        on récupère l'entité ressource en question
            $ressource = $ressourceRepository->findOneBy(['id'=>$ressource_id]);
        }

        $form = $this->createForm(ProposalType::class);
        $form->handleRequest($request);

        if ($ressource != null){

            // AJOUT DE LA PHOTO DE LA RESSOURCE
            /** @var UploadedFile $brochureFile */
            $proposalPicture = $form->get('proposal_picture')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($proposalPicture) {
                $originalFilename = pathinfo($proposalPicture->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = $originalFilename.'-'.uniqid().'.'.$proposalPicture->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $proposalPicture->move(
                        $this->getParameter('picture_proposal_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    dump($e->getMessage());
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $proposal->setProposalPicture($newFilename);
            }

            $quantity = $form->get('quantity')->getData();
            $location = $form->get('location')->getData();
            $offerorneed = $form->get('offerorneed')->getData();

            dump($user);

            if($quantity and $location){
                $user->addProposal($proposal);

                $proposal->setOfferorneed($offerorneed);
                $proposal->setRessource($ressource);
                $proposal->setQuantity($quantity);
                $proposal->setLocation($location);
                $proposal->setDone(0);

                $this->em->persist($proposal);
                $this->em->persist($user);

                $this->em->flush();

                $this->addFlash('success', 'Félicitation, votre proposition est ajoutée');

                // AJOUT A NEO4J

                $userId = strval($user->getId());
                $proposalId = strval($proposal->getId());
                $ressourceId = strval($ressource->getId());

                $proposalDirection = $proposal->getOfferorneed();
                if ($proposalDirection == 0){
                    $proposalDirection = 'NEED';
                }
                elseif ($proposalDirection == 1){
                    $proposalDirection = 'OFFEREDBY';
                }
                dump($proposalDirection);

                $client = new Client();

                $response = $client->request('POST', 'http://localhost:3000/proposal',
                    [
                        'json'    => ['userId' => $userId, 'proposalDirection' => $proposalDirection, 'proposalId' => $proposalId, 'ressourceId'=> $ressourceId]
                    ]
                );

                echo $response->getStatusCode(); // 200

                // FIN AJOUT

                return $this->redirectToRoute('proposal');
            }

        }

        return $this->render('proposal/new.html.twig', [
            'path'=>$path,
            'categorymother'=>$categoryMother,
            'categories' => $categories,
            'ressources' => $ressources,
            'category' => $category,
            'ressource' => $ressource,
            'form'=> $form->createView()
        ]);
    }

//    À REVOIR CAR SUREMENT PAS BESOIN CAR A LA RELATION ASKERS DE PROPOSALS
    /**
     * @Route ("/proposal/add/{id}", name="proposal.add", methods="POST|GET")
     * @param Proposal $proposal
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function add(Proposal $proposal, PathCategory $pathCategory, Request $request):Response // injection pour récuperer la ressource qui nous interesse
    {
        $user = $this->getUser();
        $pathCategoryRessource = $pathCategory->getPathCategory($proposal->getRessource()->getCategory());

        $proposal_added = new Proposal();

        dump($pathCategoryRessource);

//        Si la proposition sur laquelle on clic est une demande=false alors la nouvelle est une offre =true
        if ($proposal->getOfferorneed() == false){
            $offer_or_need = true ;
        }
        else {
            $offer_or_need = false ;
        }


        if ($proposal->getQuantity()==1){
            $quantity = 1;
        }
        else{
            $quantity = $request->query->get('quantity');
        }

        $form = $this->createForm(ProposalType::class, $proposal_added);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $offer_or_need = $form->getData()->getOfferOrNeed();
            $quantity = $form->getData()->getQuantity();
            $location = $form->getData()->getLocation();
//            $proposal_picture = $form->getData()->getProposalPicture();

            $ressource = $proposal->getRessource();

            dump($ressource);
            dump($proposal);
            dump($quantity);
            dump($location);

            if ($quantity and $location){
//                $proposal_added = new Proposal();
                dump($user);
                $user->addProposal($proposal_added);
                $proposal_added->setOfferorneed($offer_or_need);
                $proposal_added->setRessource($ressource);
                $proposal_added->setQuantity($quantity);
                $proposal_added->setLocation($location);
                $proposal_added->setDone(0);
//                $proposal_added->addProposalsLinked($proposal);
//                $proposal_added->setProposalPicture($proposal_picture);


                $this->em->persist($proposal_added);
                $this->em->persist($user);

                $this->em->flush();

                $this->addFlash('success', 'Félicitation, votre proposition est ajoutée');

                // AJOUT A NEO4J

                $userId = strval($user->getId());
                $proposalId = strval($proposal_added->getId());
                $ressourceId = strval($ressource->getId());

                dump($userId);
                dump($proposalId);

                $proposalDirection = $proposal_added->getOfferorneed();

                dump($proposalDirection);

                if ($proposalDirection == 0){
                    $proposalDirection = 'NEED';
                }
                elseif ($proposalDirection == 1){
                    $proposalDirection = 'OFFEREDBY';
                }
                dump($proposalDirection);

                $client = new Client();

                $response = $client->request('POST', 'http://localhost:3000/proposal',
                    [
                        'json'    => ['userId' => $userId, 'proposalDirection' => $proposalDirection, 'proposalId' => $proposalId, 'ressourceId'=> $ressourceId]
                    ]
                );

                echo $response->getStatusCode(); // 200

                // FIN AJOUT

                return $this->redirectToRoute('proposal');

            }
        }


        return $this->render('proposal/add.html.twig', [
            'offerorneed' => $offer_or_need,
//            'pathcategory' => $pathCategoryRessource,
            'proposal' => $proposal,
            'form' => $form->createView()
        ]);
    }

//    Juste pour faire des tests JS

    /**
     * @Route ("/proposal/test/{id}", name="proposal.tests", requirements={"id"="\d+"})
     * @return Response
     */

    public function idDefault(int $id = 0): Response
    {
        dump($id);

        return $this->render('proposal/tests.html.twig');
    }

    /**
     * @Route ("/proposal/test2", name="proposal.test2", requirements={"id"="\d+"})
     * @return Response
     */

    public function test2(): Response
    {
        return $this->render('proposal/test2.html.twig');
    }

    /**
     * @Route ("/proposal/tableRessources/{id}", name="table.ressources", requirements={"id"="\d+"})
     * @param Category $category
     * @param Request $request
     * @return Response
     */
    public function tableRessources(Category $category, RessourceRepository $ressourceRepository, Request $request): Response
    {
        $ressources = $ressourceRepository->findBy(['category'=>$category]);

        return $this->render('proposal/tableRessources.html.twig', [
            'category'=> $category,
            'ressources'=> $ressources
        ]);
    }

    /**
     * @Route ("/proposal/tableProposals/{categId}", name="table.proposals", requirements={"categId"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function tableProposals(CategoryRepository $categoryRepository, PathCategory $pathCategory, ProposalRepository $proposalRepository, int $categId = 0): Response
    {
        $user=$this->getUser();

        if ($user != null){
                $allProposals=$proposalRepository->findAllExcept($user);
        }
        else{
                $allProposals = $proposalRepository->findAll();
        }

        $category = null;
        $proposals = $allProposals;

        if ($categId != null)
        {
            $category = $categoryRepository->findOneBy(['id'=>$categId]);

            //ON VERIFIE SI LA CATEGORY EN INDEX EST PRESENTE DANS LE PATH CATEGORY DE CHAQUE RESSOURCE DE CHAQUE PROPOSITION
            $index = 0;
            $proposals = null;
            foreach ($allProposals as $proposal){
                $ressource = $proposal->getRessource();
                $categoryRessource = $ressource->getCategory();
                $path = $pathCategory->getPathCategory($categoryRessource);

                if (in_array($category, $path)){
                    $proposals[$index]= $proposal;
                    $index++;
                }
            }
        }
//        else
//        {
//
//        }

//        $proposals = null;
//        if ($categId != null){
//            $category = $categoryRepository->findBy(['id'=>$categId]);
//
//            if ($user != null){
//                $allProposals=$proposalRepository->findAllExcept($user);
//            }
//            else{
//                $allProposals = $proposalRepository->findAll();
//            }
//
//            //ON VERIFIE SI LA CATEGORY EN INDEX EST PRESENTE DANS LE PATH CATEGORY DE CHAQUE RESSOURCE DE CHAQUE PROPOSITION
//            $index = 0;
//            foreach ($allProposals as $proposal){
//                $ressource = $proposal->getRessource();
//                $categoryRessource = $ressource->getCategory();
//                $path = $pathCategory->getPathCategory($categoryRessource);
//
//                if (in_array($category, $path)){
//                    $proposals[$index]= $proposal;
//                    $index++;
//                }
//            }
//        }
//        else{
//            if ($user != null){
//                $proposals=$proposalRepository->findAllExcept($user);
//            }
//            else{
//                $allProposals = $proposalRepository->findAll();
//            }
//            $category = null;
//            $proposals = $allProposals
//        }

        return $this->render('proposal/tableProposals.html.twig', [
            'category'=> $category,
            'proposals'=> $proposals
        ]);
    }

}
