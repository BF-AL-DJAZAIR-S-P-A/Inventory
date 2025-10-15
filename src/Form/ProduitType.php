<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codeArticle',TextType::class,[
                'label' =>  false,
                'required' => false,


            ])
 
            ->add('codeBarre',TextType::class,[
                'label' =>  false,
                'required' => false,


            ])
            ->add('nom',TextType::class,[
                'label' =>  false

            ])
                ->add('reference',TextType::class,[
                'label' =>  false

            ])
            ->add('description',TextareaType::class,[
                'label' =>  false,
                'required' => false,

            ])
            
            ->add('unite', ChoiceType::class, [
        'choices' => [
            '' => '',
            'Pc' => 'Pc',
            'Kg' => 'Kg',
           
        ],
        'expanded' => false, // si tu veux un <select>
        'multiple' => false,
         'label' =>  false
         ])
            
           ->add('poids', NumberType::class, [
            'label' => false,
            'scale' => 2,       // nombre de décimales
            'required' => false,
            ])    
    
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'placeholder' => '',
                'label' =>  false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
