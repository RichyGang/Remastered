<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\CategoryAttribute;
use App\Entity\Proposal;
use App\Entity\Ressource;
use App\Entity\RessourceAttribute;
use http\Env\Response;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use function Sodium\add;
use Symfony\Component\Form\ChoiceList\ChoiceList;

class RessourceType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Selectionner la catégorie',
                ]);

        $builder
            ->add('description')
            ->add('ressource_picture', FileType::class, [
                'label' => 'Rajouter une photo pour la ressource',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please send valid image',
                    ])
                ],
            ]);

        $builder
            ->getForm()->getData();

        $category = $builder->getForm()->getData()->getCategory();
        dump($category);

        if ($category){
            foreach ($category->getCategoryAttributes() as $key => $value) {

                if ($symbol = $value->getUnity()){
                    $symbol = $value->getUnity()->getSymbol();
                }
                else {
                    $symbol=null;
                }

                $builder->add('ressource_attribute' . $key, TextType::class,
                    [
                        'mapped' => false,
                        'auto_initialize' => false,
                        'label' => $value->getName(),
                        'attr' => array(
                            'placeholder' => $symbol,
                        )
                    ]);



//            $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
//                'ressource_attribute' . $key,
//                TextType::class,
//                null,
//                [
//                    'mapped' => false,
//                    'auto_initialize' => false,
//                    'label' => $value->getName(),
//                    'attr' => array(
//                        'placeholder' => $value->getUnity(),
//                    )
//                ]);
//
//            $form->add($builder->getForm());

            }
        }


//        $builder
//            ->get('category')->addEventListener(
//                FormEvents::PRE_SUBMIT,
//                function (FormEvent $event) {
//                    $form = $event->getForm()->getData();
//                    dump($form);
//
////                    $this->addCategoryAttribute($form->getParent(), $form->getData());
//                }
//            );

//        foreach ($category->getCategoryAttributes() as $key => $value) {
//
//            if ($symbol = $value->getUnity()){
//                $symbol = $value->getUnity()->getSymbol();
//            }
//            else {
//                $symbol=1;
//            }
//
//            $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
//                'ressource_attribute' . $key,
//                TextType::class,
//                null,
//                [
//                    'mapped' => false,
//                    'auto_initialize' => false,
//                    'label' => $value->getName(),
//                    'attr' => array(
//                        'placeholder' => $value->getUnity(),
//                    )
//                ]);
//
//            $form->add($builder->getForm());
//
//        }
    }

    /**
     * Genere les champs attributs de la catégorie
     * @param FormInterface $form
     * @param Category $category
     */
    private function addCategoryAttribute(FormInterface $form, Category $category)
    {
        foreach ($category->getCategoryAttributes() as $key => $value) {

            if ($symbol = $value->getUnity()){
                $symbol = $value->getUnity()->getSymbol();
            }
            else {
                $symbol=1;
            }

            $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
                'ressource_attribute' . $key,
                TextType::class,
                null,
                [
                    'mapped' => false,
                    'auto_initialize' => false,
                    'label' => $value->getName(),
                    'attr' => array(
                        'placeholder' => $value->getUnity(),
                    )
                ]);

            $form->add($builder->getForm());

        }

    }

    private function addRestFormRessource(FormInterface $form, string $string)
    {
        dump($string);
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
            'ressource_attribute' . $key,
            TextType::class,
            null,
            [
                'auto_initialize' => false,
                'label' => $value->getValue(),
                'attr' => array(
                    'placeholder' => 'remplir l\'attribut',
                ),
                'mapped' => 'false'
            ]);
        $form->add($builder->getForm());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'category' => null,
            'data_class' => Ressource::class,
        ]);
    }

}
