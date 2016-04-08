<?php

namespace PhpInk\Nami\CoreBundle\Admin\Orm;

use PhpInk\Nami\CoreBundle\Model\Orm\Block;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class BlockAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', 'text', array(
                'label' => 'Title',
                'required' => false
            ))
            ->add('content', 'sonata_simple_formatter_type', array(
                'format' => 'richhtml',
                'required' => false
            ))
            ->add('template', 'text', array(
                'label' => 'Template',
                'required' => false
            ))
            ->add('plugin', 'text', array(
                'label' => 'Plugin',
                'required' => false
            ))
            ->add('page', 'sonata_type_model', array(
                'label' => 'Page',
                'class' => 'PhpInk\Nami\CoreBundle\Model\Orm\Page',
                'property' => 'slug',
                'required' => false
            ))
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
            ->add('page')
            ->add('createdBy')
            ->add('category')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('slug')
            ->add('page')
            ->add('createdAt')
        ;
    }

    public function toString($object)
    {
        return $object instanceof Block
            ? $object->getTitle()
            : 'Block'; // shown in the breadcrumb on the create view
    }
}