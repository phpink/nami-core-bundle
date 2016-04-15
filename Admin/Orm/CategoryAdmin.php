<?php

namespace PhpInk\Nami\CoreBundle\Admin\Orm;

use PhpInk\Nami\CoreBundle\Model\Orm\Category;
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
            ->add('active', 'boolean', array(
                'label' => 'Active'
            ))
            ->add('header', 'sonata_simple_formatter_type', array(
                'format' => 'richhtml',
                'required' => false
            ))
            ->add('content', 'sonata_simple_formatter_type', array(
                'format' => 'richhtml',
                'required' => false
            ))
            ->add('metaDescription', 'textarea', array('required' => false))
            ->add('metaKeywords', 'textarea', array('required' => false))
            ->add('createdAt', 'datetime', array(
                'required' => false
            ))
            ->add('createdBy', 'sonata_type_model', array(
                'label' => 'Author',
                'class' => 'PhpInk\Nami\CoreBundle\Model\Orm\User',
                'property' => 'username',
                'required' => false
            ))
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
            ->add('slug');
    }

    public function toString($object)
    {
        return $object instanceof Category
            ? $object->getName()
            : 'Category'; // shown in the breadcrumb on the create view
    }
}