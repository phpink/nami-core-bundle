<?php

namespace PhpInk\Nami\CoreBundle\Admin\Orm;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PageAdmin extends AbstractAdmin
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
            ->add('blocks', 'sonata_type_model', array(
                'label' => 'Blocks',
                'class' => 'PhpInk\Nami\CoreBundle\Model\Orm\Block',
                'property' => 'username',
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
            ->add('createdBy')
            ->add('slug')
            ->add('category')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('slug')
            ->add('createdAt')
        ;
    }
}