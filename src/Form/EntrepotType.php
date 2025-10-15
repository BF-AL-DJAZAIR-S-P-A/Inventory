<?php

namespace App\Form;

use App\Entity\Entrepot;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntrepotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('utilisateurs', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'multiple' => true,
                'required' => false, // <-- ici
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entrepot::class,
        ]);
    }
}
