<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la nouvelle catégorie enfant'
            ])
            ->add('mother', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => '',
                'label' => 'Mère',
                'required' => 'false',
                'empty_data' => 'Default value'
//                'data' => ''
            ]);

//        $builder
//            ->get('name')->addEventListener(
//                FormEvents::POST_SUBMIT,
//                function (FormEvent $event) {
//                    $form = $event->getForm();
//
//                    $this->addCategoryAttribute($form->getParent(), $form->getData());
//
////                    $form->add($builder->getForm());
//
////                    dump($form->getData());
//                }
//            );
    }

    /**
     * Rajoute un champ d'un attribut et de son unité au formulaire
     * @param FormInterface $form
     * @param Category $category
     */
    private function addCategoryAttribute(FormInterface $form, Category $category)
    {
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
            'category_attribute',
            TextType::class,
            null,
            [
                'mapped' => false,
                'auto_initialize' => false,
                'label' => 'attribut1',
                'attr' => array(
                    'placeholder' => 'remplir',
                )
            ]
        );
    }

//        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
//            'eed',
//            TextType::class,
//            null,
//            [
//                'mapped' => false,
//                'auto_initialize' => false,
//                'label' => 'attribut1',
//                'attr' => array(
//                    'placeholder' => 'remplir',
//                )
//            ]
//        );

        //Appel du form
//        $form->add($builder->getForm());

//        $builder->addEventListener(FormEvents::POST_SUBMIT,
//            function (FormEvent $event) {
//                dump($event->getForm());
//            }
//        );


        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => Category::class,
            ]);
        }

}
