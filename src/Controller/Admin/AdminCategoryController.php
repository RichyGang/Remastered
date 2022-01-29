<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\CategoryAttribute;
use App\Entity\Ressource;
use App\Entity\Unity;
use App\Form\CategoryAttributeType;
use App\Form\CategoryType;
use App\Form\CategoryType2Type;
use App\Form\RessourceType;
use App\Form\UnityType;
use App\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Constraints\All;
//use Symfony\Component\Validator\Validator\ValidatorInterface;



class AdminCategoryController extends AbstractController
{

    /**
     * @Route("admin/category", name="admin.category")
     */
    public function index(CategoryRepository $repository): Response
    {
        $categories = $repository->findAll();

        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     * @Route("admin/category/new", name="admin.category.new")
     */
    public function new(Request $request, CategoryRepository $categoryRepository):Response
    {
        $categoryMothers = $categoryRepository->findBy(['mother'=>null]);
        $form_cat = $this->createForm(CategoryType::class);
        $form_cat->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if($form_cat->isSubmitted() && $form_cat->isValid()){

            $category = new Category();

                $category->setName($form_cat->getData()->getName());
                $category->setMother($form_cat->getData()->getMother());
            $attributes = $form_cat->getData()->getMother()->getCategoryAttributes();

            foreach($attributes as $attribute)
            {
                $category->addCategoryAttribute($attribute);
            }
                $categorymother = $form_cat->getData()->getMother();
                $categorymother->addChild($category);


                $em->persist($category);
                $em->flush();

                $this->addFlash('success', 'Catégorie bien ajoutée, ajoutez-y des attributs !' );
                return $this->redirectToRoute('admin.category.new.attribute',[
                    'id' => $category->getId(),
                ], 301);

        }

        return $this->render('admin/category/new.html.twig',[
            'categorymothers'=>$categoryMothers,
            'form_cat'=>$form_cat->createView(),
            'categories'=>$categoryRepository->findAll()
        ]);

    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     * @Route("admin/category/new/attribute/{id}", name="admin.category.new.attribute")
     */
    public function newAttribute(int $id, Request $request):Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $category = $entityManager->getRepository(Category::class)->find($id);
        $attributes = $category->getCategoryAttributes();

        // Formulaire ajout attribut à cette catégorie

        $form_cat_attr = $this->createForm(CategoryAttributeType::class);
        $form_cat_attr->handleRequest($request);

        $form_unit = $this->createForm(UnityType::class);
        $form_unit->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form_unit->isSubmitted() && $form_unit->isValid()) {
            $unity = new Unity();
//            on met degree = 1 car degree 0 = category ex: volume
            $unity->setDegree(1);
            $unity->setName($form_unit->getData()->getName());
            $unity->setSymbol($form_unit->getData()->getSymbol());
//            la grandeur est la catégorie ex: Volume
            $unity->setGrandeur($form_unit->getData()->getGrandeur());

            $em->persist($unity);
            $em->flush();

        }

        if($form_cat_attr->isSubmitted() && $form_cat_attr->isValid()) {
            $category_attribute = new CategoryAttribute();
            $category_attribute->setName($form_cat_attr->getData()->getName());
            $category_attribute->setUnity($form_cat_attr->getData()->getUnity());
            $category_attribute->setFormat($form_cat_attr->getData()->getFormat());
            $category_attribute->addCategory($category);

            $em->persist($category_attribute);
            $em->flush();
        }

        if (!$category) {
            throw $this->createNotFoundException(
                'Pas de catégorie trouvée pour id '.$id
            );
        }
        return $this->render('admin/category/newAttribute.html.twig',[
            'category' => $category,
            'attributes' => $attributes,
            'form_cat_attr'=>$form_cat_attr->createView(),
            'form_unit'=>$form_unit->createView(),
        ]);
    }

    /**
     * @Route ("/admin/category/edit/{id}", name="admin.category.edit", methods="POST|GET", requirements={"id":"\d+"})
     * @IsGranted("ROLE_ADMIN")
     * @ParamConverter("category", options={"id"="id"})
     * @param Category $category
     * @param Request $request
     * @param CategoryAttribute $categoryAttribute
     * @param CategoryRepository $categoryRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Category $category, CategoryRepository $categoryRepository, Request $request) // injection pour récuperer la category qui nous interesse
    {
        $em = $this->getDoctrine()->getManager();

        $a = $categoryRepository->find($category->getId());
        $oldCategoryAttributes = $a->getCategoryAttributes()->getValues();

        foreach ($oldCategoryAttributes as $oldCategoryAttribute) {
            $oldCategoryAttribute->removeCategory($category);
            $em -> persist($oldCategoryAttribute);
        }

        $categories = $categoryRepository->findAll();
        $form = $this->createForm(CategoryType2Type::class, $category);
        $form->handleRequest($request);

        $form2 = $this->createForm(CategoryAttributeType::class);
        $form2->handleRequest($request);

        if (($form->isSubmitted() && $form->isValid())||($form2->isSubmitted() && $form2->isValid())) {

//            ICI IL FAUT CHANGER LE SENS DES FONCTIONS : AU LIEU DE FAIRE $CATEGOATTRI ->REMOVE($CATEG), FAIRE $CATEG -> REMOVE($CATEGORYATTRIBU)

            $categoryAttributes = $form->getData()->getCategoryAttributes();
            foreach ($categoryAttributes as $categoryAttribute) {
                $categoryAttribute->addCategory($category);
            }

            $newCategoryAttribute = $form2->getData();

            if ($newCategoryAttribute != null){
                dump($newCategoryAttribute);
                $category->addCategoryAttribute($newCategoryAttribute);
                $em->persist($newCategoryAttribute);
            }

            $em->persist($category);

            $em->flush();
            return $this->render('admin/category/edit.html.twig', [
                'categories' => $categories,
                'category' => $category,
                'form' => $form->createView(),
                'form2' => $form2->createView(),
            ]);
        }

        return $this->render('admin/category/edit.html.twig', [
            'categories' => $categories,
            'category' => $category,
            'form' => $form->createView(),
            'form2' => $form2->createView(),
        ]);
    }

    /**
     * @Route ("/admin/category/edit2/{id}", name="admin.category.edit2", methods="POST|GET")
     * @IsGranted("ROLE_ADMIN")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit2(Category $category, Request $request)
    {
        $form = $this->createForm(CategoryType2Type::class, $category);
        $form->handleRequest($request);

        $form2 = $this->createForm(CategoryAttributeType::class);
        $form2->handleRequest($request);

        $category = $form->getData()->getCategoryAttributes();

        return $this->render('admin/category/edit2.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'form2' => $form2->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route ("/admin/category/delete/{id}", name="admin.category.delete", requirements={"id":"\d+"})
     * @param Category $category
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Category $category):Response
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($category);
        $em->flush();

        return $this->redirectToRoute('admin.category');

    }

}
