<?php

namespace App\Form;

use App\Class\Contact;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Votre Prénom',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Votre Nom',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre Email',
                'attr' => [
                    'placeholder' => 'email@email.com',
                ]
            ])
            ->add('subject', TextType::class, [
                'label' => 'Sujet du Message',
            ])
            ->add('message', HiddenType::class, [
                'label' => 'Votre Message',
            ])
            ->add('administrator', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%ROLE_ADMIN%'); },
                'choice_label' => 'firstName',
                'label' => 'Choisir un administrateur',
                'placeholder' => 'Sélectionner un administrateur',
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
