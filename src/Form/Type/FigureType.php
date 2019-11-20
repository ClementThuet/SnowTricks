<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use App\Form\Type\UtilisateurType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Figure;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class FigureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class)
            ->add('description', TextareaType::class)
            ->add('groupe', TextType::class)
            ->add('medias', CollectionType::class,[
            'entry_type' => MediaType::class,
            'entry_options' => ['label' => false],
            'by_reference' => false,
            'allow_add' => true,
            'allow_delete' => true,
        ])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}