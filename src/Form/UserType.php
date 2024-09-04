<?php

// src/Form/UserType.php
namespace App\Form;

use App\Entity\User;
use App\Enum\Suit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('login', TextType::class)
            ->add('password', PasswordType::class)
            ->add('email', EmailType::class)
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'User' => Suit::user->value,
                    'Editor' => Suit::editor->value,
                ],
            ]);
        //->add('register', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
