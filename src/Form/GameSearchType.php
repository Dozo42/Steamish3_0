<?php

namespace App\Form;

use App\Entity\Genre;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TypeTextType::class, [
            'required'=>false,
            'label' => false,
            'attr' => [
                'placeholder' => 'Nom'
            ]
        ])
        ->add('price', IntegerType::class, [
            'required'=>false,
            'label' => false,
            'attr' => [
                'placeholder' => 'Prix'
            ]
        ])
        ->add('publishedAt', DateType::class, [
            'required'=>false,
            'label' => false,
            'widget' => 'single_text',
            'attr' => [
                'placeholder' => 'Date de publication'
            ]
        ])
        ->add('genre', EntityType::class, [
            'class' => Genre::class,
            'required' => false,
            'choice_label' => 'name',
            'label' => false
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
