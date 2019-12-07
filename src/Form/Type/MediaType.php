<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Media;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class,['attr' => ['class' => 'form-control']])
            ->add('isImage', ChoiceType::class,[
                'choices' => array('Oui' => true, 'Non' => false),
                'multiple'=>false,
                'expanded'=>true,
                //'data' => 1,
                'label' => 'Ce média est-t-il une image ?',
                'attr' => ['class' => 'custom-control-input']])
            ->add('isMainPicture', ChoiceType::class,['choices' => array('Oui' => true, 'Non' => false),
                'multiple'=>false,
                'expanded'=>true,
                'label' => 'Cette image est-t-elle l\'image principale ?',
                //'data' => 0,
                'attr' => ['class' => 'custom-control-input']])
            ->add('url', TextareaType::class, ['label' => 'URL du média','attr' => ['class' => 'form-control']])
            ->add('alt', TextType::class, ['label' => 'Description alternative','attr' => ['class' => 'form-control']])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}