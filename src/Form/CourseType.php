<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\category;
use App\Entity\level;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class
CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('small_description')
            ->add('full_description')
            ->add('duration')
            ->add('price')
            ->add('created_at', null, [
                'widget' => 'single_text'
            ])
            ->add('is_published')
            ->add('slug')
            ->add('image')
            ->add('program')
            ->add('category', EntityType::class, [
                'class' => category::class,
'choice_label' => 'id',
            ])
            ->add('level', EntityType::class, [
                'class' => level::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}
