<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
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
            ->add('isMainPicture', ChoiceType::class,[
                'choices' => array('Oui' => true, 'Non' => false),
                'multiple'=>false,
                'expanded'=>true,
                'required' => false,
                'placeholder' => false,
                'label' => 'Cette image est-t-elle l\'image principale ?',
                //'data' => 0,
                ])
            ->add('url', FileType::class, [
                'label' => 'URL du média',
                 // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                'required' => false,
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '150000k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Merci de choisir une image au format jpg ou png',
                    ])
                ],
            ])
            ->add('urlVideo', TextType::class,['attr' => ['class' => 'form-control'],'mapped'=>false,'required'=>false])
            ->add('alt', TextType::class, ['label' => 'Description alternative','attr' => ['class' => 'form-control']])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
            'allow_extra_fields'=>true
        ]);
    }
}