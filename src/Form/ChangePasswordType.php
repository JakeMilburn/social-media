<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;


class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'oldPassword',
                PasswordType::class,
                [
                    'label' => 'Current Password',
                ]
            )
            ->add(
                'newPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => 'The password fields must match.',
                    'label' => 'Password',
                    'required' => true,
                    'first_options' => ['label' => 'New Password'],
                    'second_options' => ['label' => 'Repeat Password'],
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Update',
                ]
            );
    }

}
