<?php

namespace PhpInk\Nami\CoreBundle\Admin\Orm;

use PhpInk\Nami\CoreBundle\Model\Orm\Page;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PageAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('General') // the tab call is optional
            ->with('Details', array(
                //'class'       => 'col-md-8',
                'box_class'   => 'box box-solid box-info',
                'description' => 'Page details',
            ))
                ->add('title', 'text', array(
                    'label' => 'Title',
                    'required' => false
                ))
                ->add('slug', 'text', array(
                    'label' => 'Slug',
                    'required' => false
                ))
    //            ->add('active', 'checkbox', array(
    //                'label' => 'Active'
    //            ))
                ->add('category', 'sonata_type_model', array(
                    'label' => 'Category',
                    'class' => 'PhpInk\Nami\CoreBundle\Model\Orm\Category',
                    'property' => 'name',
                    'required' => false
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
                ->add('background', 'sonata_type_model', array(
                    'label' => 'Category',
                    'class' => 'PhpInk\Nami\CoreBundle\Model\Orm\Image',
                    'property' => 'name',
                    'required' => false
                ))
            ->end()
            ->with('Blocks', array(
                //'class'       => 'col-md-8',
                'box_class'   => 'box box-solid box-warning',
                'description' => 'Blocks on the page',
            ))
                ->add('blocks')
            ->end()
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('slug')
            ->add('active')
            ->add('category')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('slug')
            ->add('active')
            ->add('createdAt')
        ;
    }

    public function toString($object)
    {
        return $object instanceof Page
            ? $object->getTitle()
            : 'Page'; // shown in the breadcrumb on the create view
    }
}