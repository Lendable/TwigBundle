<?php

declare(strict_types=1);

namespace Alpha\TwigBundle\Admin;

use Alpha\TwigBundle\Entity\Template;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Validator\Constraints\NotBlank;

class TemplateAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('name', 'text', [
                'label' => 'Name',
                'help' => 'Follow the convection: type.name.format.twig, e.g. email.welcome.txt.twig',
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('source', 'textarea', [
                'label' => 'Template',
                'constraints' => [
                    new NotBlank(),
                ]
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('name')
            ->add('source');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('name')
            ->add('source')
            ->add('lastModified');
    }

    public function prePersist($object)
    {
        assert($object instanceof Template);
        $object->setLastModifiedDate();
    }

    public function preUpdate($object)
    {
        assert($object instanceof Template);
        $object->setLastModifiedDate();
    }
}
