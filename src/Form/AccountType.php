<?php

namespace App\Form;

use App\Entity\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nom',
                ]
            ])
            ->add('email', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Mail',
                ]
            ])
            ->add('nickname', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Pseudo',
                ]
            ])
            ->add('slug', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Slug',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Soumettre',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
        ]);
    }
}
