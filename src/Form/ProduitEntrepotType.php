<?php

namespace App\Form;

use App\Entity\Entrepot;
use App\Entity\Produit;
use App\Entity\ProduitEntrepot;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitEntrepotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('produit', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'nom',
                'label' => 'Produit',
                'placeholder' => 'Sélectionnez un produit',
            ])
            ->add('entrepot', EntityType::class, [
                'class' => Entrepot::class,
                'choice_label' => 'nom',
                'label' => 'Entrepôt',
                'placeholder' => 'Sélectionnez un entrepôt',
            ])
            ->add('quantite', NumberType::class, [
                'label' => 'Quantité en stock',
                'required' => true,
                'html5' => true,
                'scale' => 2,
                'attr' => [
                    'min' => 0,
                    'step' => '0.01',
                    'placeholder' => '0.00'
                ],
            ])
            ->add('stockMinimum', NumberType::class, [
                'label' => 'Stock minimum',
                'required' => false,
                'html5' => true,
                'scale' => 2,
                'attr' => [
                    'min' => 0,
                    'step' => '0.01',
                    'placeholder' => 'Ex : 10.00'
                ],
            ])
            ->add('actif', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false,
            ])
            // Ces champs sont gérés automatiquement, donc souvent on les cache du formulaire
            ->add('createdAt', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Créé le',
                'disabled' => true, // ❗ Ne pas permettre la modification manuelle
            ])
            ->add('updatedAt', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Modifié le',
                'disabled' => true, // ❗ idem
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProduitEntrepot::class,
        ]);
    }
}
