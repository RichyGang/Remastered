<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\CategoryAttribute;
use App\Entity\Ressource;
use App\Entity\RessourceAttribute;
use App\Entity\User;
use App\Form\RessourceType;
use App\Repository\CategoryAttributeRepository;
use App\Repository\CategoryRepository;
use App\Repository\RessourceAttributeRepository;
use App\Repository\RessourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;

class RessourceController extends AbstractController
{
    /**
     * @Route("/ressource", name="ressource.index")
     */
    public function index(RessourceRepository $repository): Response
    {
        $role = null;
        $ressources = $repository->findAll();

        $user = $this->getUser();

        if ($user != null){
            $role = $user->getRoles();
            $role = $role[0];
        }

        return $this->render('ressource/index.html.twig', [
            'role'=> $role,
            'current_menu' => 'Ressource',
            'ressources'=> $ressources,
        ]);
    }

    /**
     * @Route("/ressource/edit/{id}", name="ressource.edit")
     */
    public function update(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $ressource =$entityManager->getRepository(Ressource::class)->find($id);

        if (!$ressource) {
            throw $this->createNotFoundException(
                'Pas de ressource trouvée pour id '.$id
            );
        }

        return $this->render('ressource/update.html.twig', [
            'current_menu' => 'Ressource',
        ]);

    }

    /**
     * @Route("/ressource/{slug}-{id}", requirements={"slug": "[a-z0-9\-]*"}, name="ressource.show")
     * @param Ressource $ressource
     * @return Response
     */
    public function show(Ressource $ressource, string $slug, RessourceRepository $repository, CategoryAttributeRepository $attributeRepository): Response //le nom des variables doivent etre ceux des parametres de la route dans l'annotation | mais ici on a mis l'injection de la Ressource et donc ca fait le find tout seul
    {
        $ressources = $repository->findAll();
        if($ressource->getSlug() !== $slug)
        {
            return $this->redirectToRoute('ressource.show',[
                'id' => $ressource->getId(),
                'slug' => $ressource->getSlug()
            ], 301);
        }

        return $this->render('ressource/show.html.twig', [
            'ressource' => $ressource, //que je renvoies à ma vue
            'current_menu' => 'Ressources',
            'ressources' => $ressources,
            'category_attributes' => $attributeRepository->findAll()
        ]);
    }

    /**
     * @Route ("/ressource/new/{categId}", name="ressource.new", requirements={"categId"="\d+"})
     * @param Category $category
     * @param CategoryRepository $categoryRepository
     * @param Request $request
     * @return Response
     */
    public function new(CategoryRepository $categoryRepository, RessourceRepository $ressourceRepository, Request $request, int $categId = 0):Response
    {
        $categoryMother = $categoryRepository->findBy(['mother'=>null]);

        $category = null;
        $ressource = new Ressource();

        if ($categId != 0){
            $category = $categoryRepository->find($categId);
            $ressource->setCategory($category);
        }

        $form_ress = $this->createForm(RessourceType::class, $ressource);

        $form_ress->handleRequest($request);

        if($form_ress->isSubmitted() && $form_ress->isValid()){

            $em = $this->getDoctrine()->getManager();

            // Récupération des retours du form qui sont mappés dans Ressource
            $ressource = $form_ress->getData();

            // Récupération de la category rentrée dans le form
            $category = $form_ress->get('category')->getData();

            dump($form_ress->getData());

            if($ressource->getCategory()){

                // Pour chaque attribut de la catégorie on va creer une ressource-attr et lier la valeur de l'input avec l'id categ attr correspondant
                foreach($category->getCategoryAttributes() as $cle => $valeur){
                    $value_attribute = $form_ress->get('ressource_attribute'.$cle)->getData();
                    dump($value_attribute);

                    // Condition pour ne pas executer cette commande sans que les valeurs desattributs ne soient submit
                    if ($value_attribute){

                        $ressource_attribute = new RessourceAttribute();
                        $ressource_attribute->setCategoryAttribute($valeur);
                        $ressource_attribute->setValue($value_attribute);
                        $ressource_attribute->setUnity(null);

                        // Par contre on lie la ressource et la ressource attr en l'ajoutant à l'objet Ressource
                        $ressource_attribute->addRessourceAttribute($ressource);
                        $ressource->addRessourceAttribute($ressource_attribute);

                        $em->persist($ressource_attribute);

//                        $em->flush();
                    }
                }
            }

            if ($ressource->getDescription()){

                // AJOUT DE LA PHOTO DE LA RESSOURCE
                /** @var UploadedFile $brochureFile */
                $pictureRessource = $form_ress->get('ressource_picture')->getData();

                // this condition is needed because the 'brochure' field is not required
                // so the PDF file must be processed only when a file is uploaded
                if ($pictureRessource) {
                    $originalFilename = pathinfo($pictureRessource->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $newFilename = $originalFilename.'-'.uniqid().'.'.$pictureRessource->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $pictureRessource->move(
                            $this->getParameter('picture_ressource_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        dump($e->getMessage());
                        // ... handle exception if something happens during file upload
                    }

                    // updates the 'brochureFilename' property to store the PDF file name
                    // instead of its contents
                    $ressource->setRessourcePicture($newFilename);
                }

                // AJOUT DU USER QUI AJOUTE LA RESSOURCE, OU LA MODIFIE
//                /** @var User $user */
//                $user = $this->getUser();
//                $user->addRessources($ressource);

//                $a = $ressource.
                $em->persist($ressource);
                $em->flush();

                // AJOUT DE LA RESSOURCE DANS NEO4J

                $client = new Client();

                // Pour transformer le id de INT a STR pour pas avoir la décimale dans Neo4j
                $ressourceId = strval($ressource->getId());

//                $ressourceAttr = $ressource->getRessourceAttributes()->getValues();
//                $ressourceAttr_1 = $ressourceAttr[0]->getValue();
//                $ressourceAttr_2 = $ressourceAttr[1]->getValue();

//                if ($ressourceId){
//                    $url = 'http://localhost:3000/';
//
//                    $response = $client->request( 'POST', $url.'ressource/add',
//                        [
//                            'json' => [
//                                'ressourceId' => $ressourceId
//                            ]
//                        ]);
//                }



                // FIN AJOUT

                $this->addFlash('success', 'Ressource bien ajoutée!' );
                return $this->redirectToRoute('ressource.index');
            }
        }

        return $this->render('ressource/new.html.twig',[
            'categorymother' => $categoryMother,
            'category'=>$category,
            'form_ress'=>$form_ress->createView(),
            'ressource'=>$ressource

        ]);
    }

    /**
     * @Route ("/ressource/form/{categId}", name="ressource.form", requirements={"categId"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function form(Request $request, RessourceRepository $ressourceRepository, RessourceAttributeRepository $ressourceAttributeRepository, CategoryRepository $categoryRepository, CategoryAttributeRepository $attr, int $categId = 0):Response
    {
    dump("CORRIGER L'AJOUT DE L'IMAGE DE LA RESSOURCE");

        $categoryMother = $categoryRepository->findBy(['mother'=>null]);

        $category = null;
        $ressource = new Ressource();

        if ($categId != 0){
            $category = $categoryRepository->find($categId);
            $ressource->setCategory($category);
        }

        $form_ress = $this->createForm(RessourceType::class, $ressource);

        $form_ress->handleRequest($request);

//        if($form_ress->isSubmitted() && $form_ress->isValid()){
//
//            $em = $this->getDoctrine()->getManager();
//
//            // Récupération des retours du form qui sont mappés dans Ressource
//            $ressource = $form_ress->getData();
//
//            // Récupération de la category rentrée dans le form
//            $category = $form_ress->get('category')->getData();
//
//            dump($form_ress->getData());
//
//            if($ressource->getCategory()){
//
//                // Pour chaque attribut de la catégorie on va creer une ressource-attr et lier la valeur de l'input avec l'id categ attr correspondant
//                foreach($category->getCategoryAttributes() as $cle => $valeur){
//                    $value_attribute = $form_ress->get('ressource_attribute'.$cle)->getData();
//                    dump($value_attribute);
//
//                    // Condition pour ne pas executer cette commande sans que les valeurs desattributs ne soient submit
//                    if ($value_attribute){
//
//                        $ressource_attribute = new RessourceAttribute();
//                        $ressource_attribute->setCategoryAttribute($valeur);
//                        $ressource_attribute->setValue($value_attribute);
//                        $ressource_attribute->setUnity(null);
//
//                        // Par contre on lie la ressource et la ressource attr en l'ajoutant à l'objet Ressource
//                        $ressource_attribute->addRessourceAttribute($ressource);
//
//                        $em->persist($ressource_attribute);
//                        $em->flush();
//                    }
//                }
//            }
//
//            if ($ressource->getDescription()){
//
//                // AJOUT DE LA PHOTO DE LA RESSOURCE
//                /** @var UploadedFile $brochureFile */
//                $pictureRessource = $form_ress->get('ressource_picture')->getData();
//
//                // this condition is needed because the 'brochure' field is not required
//                // so the PDF file must be processed only when a file is uploaded
//                if ($pictureRessource) {
//                    $originalFilename = pathinfo($pictureRessource->getClientOriginalName(), PATHINFO_FILENAME);
//                    // this is needed to safely include the file name as part of the URL
//                    $newFilename = $originalFilename.'-'.uniqid().'.'.$pictureRessource->guessExtension();
//
//                    // Move the file to the directory where brochures are stored
//                    try {
//                        $pictureRessource->move(
//                            $this->getParameter('picture_ressource_directory'),
//                            $newFilename
//                        );
//                    } catch (FileException $e) {
//                        dump($e->getMessage());
//                        // ... handle exception if something happens during file upload
//                    }
//
//                    // updates the 'brochureFilename' property to store the PDF file name
//                    // instead of its contents
//                    $ressource->setRessourcePicture($newFilename);
//                }
//
//////                 AJOUT DU USER QUI AJOUTE LA RESSOURCE, OU LA MODIFIE
////                /** @var User $user */
////                $user = $this->getUser();
////                $user->addRessources($ressource);
//
//                $em->persist($ressource);
//                $em->flush();
//
//                $this->addFlash('success', 'Ressource bien ajoutée!' );
//                return $this->redirectToRoute('ressource.index');
//            }
//        }

        return $this->render('ressource/form.html.twig', [
            'category'=>$category,
//            'categoryattributevaluestable'=>$categoryAttributeValuesTable,
            'form_ress'=>$form_ress->createView(),
        ]);

    }

    /**
     * @Route ("/ressource/add", name="ressource.add")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request, CategoryRepository $categoryRepository, CategoryAttributeRepository $attr):Response
    {
        $categoryID = $request->query->get('categoryID');
        $category = $categoryRepository->findOneBy(['id' => $categoryID]);

        $categoryAttr = null;

        if ($categoryID !== null){
            $categoryAttr = $category->getCategoryAttributes();
        }

        return $this->render('ressource/add.html.twig',[
            'category' => $categoryRepository->findBy([
                'mother' => null
            ]),
            'category_attr' => $categoryAttr,
            'category_ID' => $categoryID
        ]);

    }



}
