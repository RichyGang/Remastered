<?php

namespace App\Controller\Admin;

use App\Entity\Ressource;
use App\Entity\RessourceAttribute;
use App\Form\RessourceType;
use App\Repository\ProposalRepository;
use App\Repository\RessourceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AdminRessourceController extends AbstractController {

    /**
     * @var RessourceRepository
     */
    private $repository;

    public function __construct(RessourceRepository $repository) //on injecte le repository concerné
    {
        $this->repository = $repository;
    }

    /**
     * @Route ("/admin", name ="admin")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(RessourceRepository $ressourceRepository, ProposalRepository $proposalRepository)
    {

        return $this->render('admin/index.html.twig', [
            'ressources' => $ressourceRepository->findAll(),
            'proposals' => $proposalRepository->findAll()
        ]);
    }

    /**
     * @Route ("/admin/ressource", name ="admin.ressource")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ressource() //va permettre de récupérer l'ensemble des biens dont il va falloir le repository
    {
        $ressources = $this->repository->findAll();
        return $this->render('admin/ressource/index.html.twig', compact('ressources'));
    }

    /**
     * @Route ("/admin/ressource/{id}/edit", name="admin.ressource.edit", methods="POST|GET")
     * @IsGranted("ROLE_ADMIN")
     * @param Ressource $ressource
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Ressource $ressource, Request $request) // injection pour récuperer la ressource qui nous interesse
    {
        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);

        $category = $form->getData()->getCategory();
        $ressource_attribute = $form->getData()->getRessourceAttributes();

        if($category){
//            dump($category->getCategoryAttributes());

            // Pour chaque attribut de la catégorie on va creer une ressource-attr et lier la valeur de l'input avec l'id categ attr correspondant
            foreach($ressource_attribute as $cle => $valeur){
//                $value_attribute = $form->get('ressource_attribute'.$cle)->getData();

                // Condition pour ne pas executer cette commande sans que les valeurs desattributs ne soient submit
                if ($ressource_attribute){

//                        $ressource_attribute->setCategoryAttribute($valeur);
//                        $ressource_attribute->setValue($value_attribute);
//
//                        // Par contre on lie la ressource et la ressource attr en l'ajoutant à l'objet Ressource
//                        $ressource->addRessourceAttribute($ressource_attribute);

//                    $em->persist($ressource_attribute);
//                    $em->flush();
                }
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $ressource = $form->getData();
            dump($ressource);
            $category = $ressource->getCategory();

            $em = $this->getDoctrine()->getManager();

            $em->persist($ressource);
            $em->flush();
            return $this->redirectToRoute('ressource.index');
        }

        return $this->render('admin/ressource/edit.html.twig', [
            'ressource' => $ressource,
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route ("/admin/{id}/delete", name="admin.ressource.delete")
     * @param Ressource $ressource
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Ressource $ressource):Response
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($ressource);
        $em->flush();

        return $this->redirectToRoute('admin.ressource');

    }

}