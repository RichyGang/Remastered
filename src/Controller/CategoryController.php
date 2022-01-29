<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryAttributeRepository;
use App\Repository\CategoryRepository;
use App\Repository\RessourceAttributeRepository;
use App\Repository\RessourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'category')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    /**
     * @Route ("/category/choiceCategory/{id}", name="choiceCategory", requirements={"id"="\d+"})
     * @param Category $category
     * @param Request $request
     * @return Response
     */
    public function choiceCategory(CategoryRepository $categoryRepository, CategoryAttributeRepository $categoryAttributeRepository, RessourceAttributeRepository $ressourceAttributeRepository, RessourceRepository $ressourceRepository, Category $category, Request $request): Response
    {
        $categories = $categoryRepository->findBy(['mother'=>$category]);
        $ressources = $ressourceRepository->findBy(['category'=>$category]);

        $categoryAttributes = $category->getCategoryAttributes();
        $index = 0;
        if ($categoryAttributes->getValues()){
            $categoryAttributeValuesTable = [];
            foreach ($categoryAttributes as $categoryAttribute)
            {
                $ressourceAttributeValues = $ressourceAttributeRepository->findby(['categoryAttribute'=>$categoryAttribute]);
                dump($ressourceAttributeValues);

                foreach ($ressourceAttributeValues as $ressourceAttributeValue){
                    if ($category == $ressourceAttributeValue->getCategoryAttribute()->getCategory()){
                        $categoryAttributeValuesTable[$index] = array_push($categoryAttributeValuesTable[$index], $ressourceAttributeValue) ;
                    }
                }

                $index++;
            }
        }
        else{
            $categoryAttributeValuesTable = null;
        }

        return $this->render('category/choiceCategory.html.twig', [
            'categoryattributevaluestable'=>$categoryAttributeValuesTable,
            'ressources' => $ressources,
            'category' => $category,
            'categories' => $categories
        ]);
    }
    /**
     * @Route ("/category/pathCategory/{id}", name="pathCategory", requirements={"id"="\d+"})
     * @param Category $category
     * @param Request $request
     * @return Response
     */
    public function pathCategory(Category $category, Request $request): Response
    {

        //        CONSTRUCTION DU CHEMIN DE LA CATEGORIE
//        $category = $categoryRepository->find($categId);
        $pathCategory = [$category];
        while ($category->getMother() != null){
            array_push($pathCategory, $category->getMother());
            $category = $category->getMother();
        }
        $pathCategory = array_reverse($pathCategory);
        dump($pathCategory);
        //            END

        return $this->render('category/pathCategory.html.twig', [
            'category'=> $category,
            'pathcategory'=> $pathCategory
        ]);
    }

}
