<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Figure;

class UpdateFigureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class)
            ->add('description', TextareaType::class)
            /*->add('groupe', EntityType::class, [
                'class' => Figure::class,
                'choice_label' => 'groupe',
                //'placeholder' => 'Autre groupe',
                'row_attr' => ['class' => 'dropdownGroupe'],
                //'choices' => ['Yes' => true]
            ])*/
            ->add('groupe', TextType::class)
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