<?php

namespace App\Form;

use App\Entity\RessourceAttribute;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RessourceAttributeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value')
            ->add('categoryAttribute')
            ->add('ressource')
            ->add('unity')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RessourceAttribute::class,
        ]);
    }
}
