<?php

namespace App\Form;

use App\Entity\Comment;
use App\Enum\Status;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CommentType extends AbstractType
{

    public function __construct(private Security $security, )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('text');

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder->add('status', ChoiceType::class, [
                'choices' => [
                    'Approved' => Status::approved->value,
                    'Rejected' => Status::rejected->value,

                ],
            ]);
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}