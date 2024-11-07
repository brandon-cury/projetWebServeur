<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Trait\ReadTrait;
use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    use ReadTrait;
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setFormTypeOption('disabled', true),
            ImageField::new('image')->setBasePath('/images/category/')->setUploadDir('assets/images/category'),
            TextField::new('name'),
            CollectionField::new('courses')->formatValue(function ($value, $entity) {return count($entity->getCourses());}),
        ];
    }

}
