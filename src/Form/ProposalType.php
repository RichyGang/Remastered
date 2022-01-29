<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\CategoryAttribute;
use App\Entity\Proposal;
use App\Entity\Ressource;
use App\Entity\RessourceAttribute;
use App\Repository\CategoryRepository;
use App\Repository\RessourceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProposalType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('offerorneed', ChoiceType::class, array(
                'choices' => array(
                    'Offrir' => true,
                    'Demander' => false
                ),
                'expanded' => true,
                'multiple' => false,
                'label'=>' '
            ))
            ->add('location')
            ->add('quantity')
            ->add('proposal_picture', FileType::class, [
                'label' => 'Rajouter une photo pour la proposition',

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
                        'mimeTypesMessage' => 'Please send valid image pute',
                    ])
                ],
            ]);

    }

    /**
     * Rajoute un champs ressource au formulaire
     * @param FormInterface $form
     * @param Category $category
     */
    private function addRessourceField(FormInterface $form, Category $category)
    {

        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
            'ressource',
            EntityType::class,
            null,
            [
                'class' => Ressource::class,
                'placeholder' => 'Selectionner la ressource',
                'choices' => $category->getRessources(),
                'choice_label' => 'description',
                'mapped' => false,
                'required' => false,
                'auto_initialize' => false,
            ]
        );

        $form->add($builder->getForm());

//        Lorque je soumets la ressource
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
//                je vais recuperer le formulaire et appeler la fonction addressourceattributes

                $form = $event->getForm();

                if($form->getData() !== null){
                    $this->addRessourceAttribute($form->getParent(), $form->getData());
                }
            }
        );

        $form->add($builder->getForm());

    }

    /**
     * Genere les champs attributs de la ressource
     * @param FormInterface $form
     * @param Ressource $ressource
     */
    private function addRessourceAttribute(FormInterface $form, Ressource $ressource)
    {
        foreach($ressource->getCategory()->getCategoryAttributes() as $key => $value){
            $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
                'Attributs'.$key,
                TextType::class,
                null,
                [
                    'mapped' => false,
                    'auto_initialize' => false,
                    'label' => $value->getName(),
                    'attr' => array(
                        'placeholder' => 'remplir l\'attribut',
                    )
                ]);

            $form->add($builder->getForm());
        }

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {

                $form = $event->getForm();

                if($form->getData() !== null){
                    dump($form->getData());
                }
            }
        );

        $form->add($builder->getForm());

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Proposal::class,
        ]);
    }
}
