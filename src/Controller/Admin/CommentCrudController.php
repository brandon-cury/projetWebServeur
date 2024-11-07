<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Trait\ReadTrait;
use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\TextEditorType;

class CommentCrudController extends AbstractCrudController
{
    use ReadTrait;
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }


    public function configureFields(string $pageName): iterable
    {
        //ID
        //Created At
        //Is Published
        //Rating
        return [
            IdField::new('id')->setFormTypeOption('disabled', true),
            TextField::new('content')
                ->formatValue(function ($value, $entity) {
                    return strip_tags($value, '<p><a><b><i><ul><ol><li>');
                })
                ->setFormTypeOption('disabled', true),
            IntegerField::new('rating')->setFormTypeOption('disabled', true),
            AssociationField::new('parent')->setFormTypeOption('disabled', true),
            DateTimeField::new('created_at')->setFormTypeOption('disabled', true),
            AssociationField::new('user')->setLabel('Auteur')->setFormTypeOption('disabled', true),
            AssociationField::new('course')->setLabel('Cours')->setFormTypeOption('disabled', true),
            BooleanField::new('is_published'),
        ];
    }

}
