<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'attr' => ['autofocus' => true],
                    'label' => 'Email',
                ]
            )
            ->add(
                'username',
                TextType::class,
                [
                    'label' => 'Username',
                ]
            )
            ->add(
                'password',
                PasswordType::class,
                [
                    'label' => 'Password',
                ]
            )
            ->add(
                'profilePicture',
                FileType::class,
                [
                    'label' => 'Profile Picture',
                    'required' => false,
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Register',
                ]
            );
    }

}
