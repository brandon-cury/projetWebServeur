<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Trait\ReadTrait;
use App\Entity\News;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class NewsCrudController extends AbstractCrudController
{
    use ReadTrait;
    public static function getEntityFqcn(): string
    {
        return News::class;
    }


    public function configureFields(string $pageName): iterable
    {
        //ID
        //Name
        //Created At
        //Content
        //Image
        return [
            IdField::new('id')->setFormTypeOption('disabled', true),
            ImageField::new('image')->setBasePath('/images/news/')->setUploadDir('assets/images/news'),
            TextField::new('name'),
            TextEditorField::new('content'),
            DateField::new('created_at'),
        ];
    }

}
