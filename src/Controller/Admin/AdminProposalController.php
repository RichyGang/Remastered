<?php

namespace App\Controller\Admin;

use App\Entity\Proposal;
use App\Entity\Ressource;
use App\Entity\RessourceAttribute;
use App\Form\ProposalType;
use App\Form\RessourceType;
use App\Repository\CategoryRepository;
use App\Repository\ProposalRepository;
use App\Repository\RessourceRepository;
use Doctrine\Common\Annotations\Annotation\Required;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class AdminProposalController extends AbstractController {

    /**
     * @var ProposalRepository
     */
    private $repository;

    public function __construct(ProposalRepository $repository) //on injecte le repository concerné
    {
        $this->repository = $repository;
    }
    /**
     * @Route("admin/proposal/{categId}", name="admin.proposal", requirements={"categId"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function index(ProposalRepository $repository, CategoryRepository $categoryRepository, Request $request, int $categId = 0): Response
    {
        if ($categId != 0){
            $category = $categoryRepository->find($categId);
        }
        else{
            $category = null;
        }

        $categoryMother = $categoryRepository->findBy(['mother'=>null]);

        $proposals = $repository->findAll();
        dump($proposals);

        return $this->render('admin/proposal/index.html.twig', [
            'categorymother' => $categoryMother,
            'category' => $category,
            'proposals' => $proposals
        ]);
    }

    /**
     * @Route ("/admin/proposal/edit/{id}", name="admin.proposal.edit", methods="POST|GET")
     * @IsGranted("ROLE_ADMIN")
     * @param Proposal $proposal
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Proposal $proposal, CategoryRepository $categoryRepository, Request $request) // injection pour récuperer la ressource qui nous interesse
    {
        $form_proposal = $this->createForm(ProposalType::class, $proposal);
        $form_proposal->handleRequest($request);

        $ressource = $form_proposal->getData()->getRessource();
        $category = $ressource->getCategory();
        $form_ressource = $this->createForm(RessourceType::class, $ressource);

        if ($form_proposal->isSubmitted() && $form_proposal->isValid()) {
            $proposal = $form_proposal->getData();
//            dump($proposal);

            $em = $this->getDoctrine()->getManager();

// AJOUT DE LA PHOTO DE LA PROPOSITION
            /** @var UploadedFile $brochureFile */
            $proposalPicture = $form_proposal->get('proposal_picture')->getData();

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

            $em->flush();
            return $this->redirectToRoute('proposal');
        }

        return $this->render('admin/proposal/edit.html.twig', [
            'category' => $category,
            'categories' => $categoryRepository->findAll(),
            'form_proposal' => $form_proposal->createView(),
            'form_ressource' => $form_ressource->createView()

        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route ("/admin/proposal/delete/{id}", name="admin.proposal.delete")
     * @param Proposal $proposal
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Proposal $proposal):Response
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($proposal);
        $em->flush();

        return $this->redirectToRoute('admin.proposal');

    }

}