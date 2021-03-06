<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PostType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'attr' => ['autofocus' => true],
                    'label' => 'Title',
                ]
            )
            ->add(
                'body',
                TextareaType::class,
                [
                    'label' => 'Body text',
                ]
            )
            ->add(
                'images',
                FileType::class,
                [
                    'label' => 'Images',
                    'required' => false,
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Post',
                ]
            );
    }

}
