<?php

namespace App\Form;

use App\Entity\Entrepot;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'choices'  => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true, // cases à cocher
            ])
            ->add('password', PasswordType::class, [
                'required' => false, // permet de ne pas modifier le mot de passe si vide
            ])
            ->add('isVerified')
            ->add('nomComplet')
            ->add('entrepots', EntityType::class, [
                'class' => Entrepot::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true, // true = checkbox, false = select multiple
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
