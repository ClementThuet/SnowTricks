<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class,[
                'constraints' => [
                new NotBlank()]
            ])
            ->add('password', PasswordType::class,[
                'constraints' => [
                new NotBlank()]
            ])
            ->add('send', SubmitType::class, ['label' => 'Me connecter']);
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
{
    $resolver->setDefaults(array(            
        'cascade_validation' => true,
    ));
}
}