<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', PasswordType::class, ['label' => 'Mot de passe',
                'constraints' => [new NotBlank()]])
            ->add('passwordCheck', PasswordType::class, ['label' => 'Vérification du mot de passe',
                'constraints' => [new NotBlank()]])    
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
       
    }
}