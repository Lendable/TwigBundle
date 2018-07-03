<?php

namespace Alpha\TwigBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Validator\Constraints\NotBlank;

class TemplateAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', 'text', [
                'label' => 'Name',
                'help' => 'Follow the convection: type.name.format.twig, e.g. sms.loan_declined.txt.twig',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('source', 'textarea', [
                'label' => 'Template',
                'constraints' => [
                    new NotBlank()
                ]
            ]);
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('source');
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('source')
            ->add('lastModified');
    }

    public function prePersist($template)
    {
        $template->setLastModifiedDate();
    }

    public function preUpdate($template)
    {
        $template->setLastModifiedDate();
    }
}
