<?php

namespace App\Form;

use App\Entity\InventaireItem;
use App\Entity\Produit;
use App\Entity\ProduitEntrepot;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class InventaireItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ✅ Sélection du produit
            ->add('produit', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez un produit',
                'label' => 'Produit',
            ])

            // ✅ Optionnel : si tu veux rattacher à un stock précis dans un entrepôt
            ->add('produitEntrepot', EntityType::class, [
                'class' => ProduitEntrepot::class,
                'choice_label' => function (ProduitEntrepot $pe) {
                    return sprintf('%s — %s (Stock: %.2f)',
                        $pe->getProduit()->getNom(),
                        $pe->getEntrepot()->getNom(),
                        $pe->getQuantite()
                    );
                },
                'placeholder' => 'Aucun entrepôt sélectionné',
                'required' => false,
                'label' => 'Stock / Entrepôt',
            ])

            // ✅ Quantité réellement comptée
            ->add('quantiteReelle', NumberType::class, [
                'label' => 'Quantité réelle comptée',
                'scale' => 2,
                'required' => true,
                'attr' => [
                    'min' => 0,
                    'step' => '0.01',
                    'placeholder' => 'Saisir la quantité réelle',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InventaireItem::class,
        ]);
    }
}
