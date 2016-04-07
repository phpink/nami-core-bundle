<?php

namespace PhpInk\Nami\CoreBundle\Admin\Orm;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CategoryAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', 'text', array('label' => 'Name'))
            ->add('title', 'text', array('label' => 'Title'))
            ->add('createdBy', 'sonata_type_model', array(
                'label' => 'Author',
                'class' => 'PhpInk\Nami\CoreBundle\Model\Orm\User',
                'property' => 'username',
                'required' => false
            ))
            ->add('header', 'textarea', array('required' => false)) //if no type is specified, SonataAdminBundle tries to guess it
            ->add('content')
            ->add('metaDescription', 'textarea', array('required' => false))
            ->add('metaKeywords', 'textarea', array('required' => false))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('slug')
            ->add('createdBy')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('title')
            ->add('slug')
        ;
    }
}