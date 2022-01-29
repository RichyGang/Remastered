<?php

namespace App\Form;

use App\Entity\CategoryAttribute;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryAttributeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label'=> "Nom"
            ])
            ->add('unity')
            ->add('format', ChoiceType::class, [
                'choices'=>[
                    'Principaux formats'=>[
                        'string' => 'string',
                        'text' => 'text',
                        'booléen' => 'boolean',
                        'entier' => 'integer',
                        'flottant' => 'float',
                    ],
                    'Dates et temps'=>[
                        'datetime' => 'datetime',
                        'date' => 'date',
                        'time' => 'time',
                        'dateinterval' => 'dateinterval',
                    ]
                ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CategoryAttribute::class,
        ]);
    }
}
