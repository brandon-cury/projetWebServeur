<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Trait\ReadTrait;
use App\Entity\Course;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\Validator\Constraints\File;


class CourseCrudController extends AbstractCrudController
{
    use ReadTrait;
    public static function getEntityFqcn(): string
    {
        return Course::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setFormTypeOption('disabled', true),
            ImageField::new('image')->setBasePath('/images/cours/')->setUploadDir('assets/images/cours'),
            TextField::new('Name'),
            TextField::new('small_description')
                    ->setLabel('Courte description')
                    ->hideOnIndex(),
            TextEditorField::new('full_description')
                    ->setLabel('Longue description')
                    ->hideOnIndex(),
            TextField::new('Duration'),
            AssociationField::new('category'),
            AssociationField::new('level')->setLabel('Niveau'),
            NumberField::new('Price'),
            DateTimeField::new('Created_at'),
            //debut du champ program
            ImageField::new('program')
                ->hideOnIndex()
                ->hideOnDetail()
                ->setLabel('program')
                ->setBasePath('programs')
                ->setUploadDir('public/programs')
                //->setFormTypeOption('multiple', true)
                ->setFileConstraints(new File([     // By adding this constraint i was able to upload any kind of file
                    'mimeTypes' => [
                        'application/pdf',
                        'application/x-pdf',
                        'application/msword',
                        'application/vnd.ms-excel'
                    ],
                    'mimeTypesMessage' => 'Documento no válido, solo se permiten PDF, DOC o XLS',
                ])),
            UrlField::new('program')
                ->setLabel('program')
                ->onlyOnIndex()
                ->formatValue(function ($value, $entity) {
                    if($value) return sprintf('<a href="/programs/%s" target="_blank">Télécharger</a>', $value);
                    return null;
                }),
            TextEditorField::new('program')
                ->setLabel('program')
                ->onlyOnDetail()
                ->formatValue(function ($value, $entity) {
                    return sprintf('<a href="/programs/%s" target="_blank">Télécharger</a>', $value);
                }),
            //fin du champ program
            CollectionField::new('comments')
                ->formatValue(function ($value, $entity) {
                    return count($entity->getComments());
                })
                ->setFormTypeOption('disabled', true),
            BooleanField::new('is_published'),
        ];
    }

}
