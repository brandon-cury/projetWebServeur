<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Trait\ReadTrait;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    use ReadTrait;
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setFormTypeOption('disabled', true),
            ImageField::new('image')->setFormTypeOption('disabled', true)->setBasePath('/images/avatar/')->setUploadDir('assets/images/avatar'),
            TextField::new('firstName')->setFormTypeOption('disabled', true),
            TextField::new('lastName')->setFormTypeOption('disabled', true),
            EmailField::new('email')->setFormTypeOption('disabled', true),
            DateTimeField::new('createdAt')->setFormTypeOption('disabled', true),
            BooleanField::new('isDisabled'),
        ];
    }

}
