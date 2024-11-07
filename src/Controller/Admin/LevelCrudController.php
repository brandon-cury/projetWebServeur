<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Trait\ReadTrait;
use App\Entity\Level;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LevelCrudController extends AbstractCrudController
{
    use ReadTrait;
    public static function getEntityFqcn(): string
    {
        return Level::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('name'),
            TextField::new('prerequisite'),
            CollectionField::new('courses')->formatValue(function ($value, $entity) {return count($entity->getCourses());}),
        ];
    }

}
