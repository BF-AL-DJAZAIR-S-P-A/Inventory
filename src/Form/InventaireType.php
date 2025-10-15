<?php

namespace App\Form;

use App\Entity\Inventaire;
use App\Entity\Entrepot;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l’inventaire',
                'attr' => ['placeholder' => 'Ex : Inventaire octobre 2025'],
            ])
            ->add('entrepot', EntityType::class, [
                'class' => Entrepot::class,
                'choice_label' => 'nom',
                'label' => 'Entrepôt concerné',
                'placeholder' => 'Sélectionnez un entrepôt',
                'required' => true,
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('dateFin', DateTimeType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En cours' => Inventaire::STATUT_EN_COURS,
                    'Terminé' => Inventaire::STATUT_TERMINE,
                    'Validé' => Inventaire::STATUT_VALIDE,
                    'Annulé' => Inventaire::STATUT_ANNULE,
                    'Appliqué' => Inventaire::STATUT_APPLIQUE,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inventaire::class,
        ]);
    }
}
