<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\category;
use App\Entity\level;
use App\Repository\CategoryRepository;
use App\Repository\LevelRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichFileType;

class
CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('small_description', TextType::class)
            ->add('full_description', HiddenType::class)
            ->add('duration', TextType::class)
            ->add('price', NumberType::class)
            ->add('schedule',  TextType::class, [
                'label' => 'Horaire',
            ])
            /*
            ->add('is_published', CheckboxType::class,)
            */
            ->add('imageFile', VichFileType::class, [
                'required' => false,
                'label' => 'Image',
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            ],
                        'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG, GIF)', ]) ],
            ])
            ->add('programFile', VichFileType::class,
                [ 'required' => false,
                    'constraints' => [
                        new File(
                            [
                                'maxSize' => '1024k',
                                'mimeTypes' => ['application/pdf', 'application/x-pdf'],
                                'mimeTypesMessage' => 'Please upload a valid PDF document',
                            ]
                        ),
                    ],
                ]
            )
            ->add('category', EntityType::class, [
                'class' => category::class,
                'choice_label' => 'name',
                'query_builder' => function (CategoryRepository $er) {
                    return $er->createQueryBuilder('c')
                    ->where('c.name IS NOT NULL');
                },
                'placeholder' => 'Choisissez une catégorie',

            ])
            ->add('level', EntityType::class, [
                'class' => level::class,
                'choice_label' => 'name',
                'query_builder' => function (LevelRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.name IS NOT NULL');
                },
                'placeholder' => 'Choisissez un niveau',
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
