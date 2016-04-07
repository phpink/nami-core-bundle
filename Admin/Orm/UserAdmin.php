<?php

namespace PhpInk\Nami\CoreBundle\Admin\Orm;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class UserAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('username', 'text', array('label' => 'Username'))
            ->add('firstName', 'text', array('label' => 'First name'))
            ->add('lastName', 'text', array('label' => 'Last name'))
/*            ->add('active', 'boolean', array(
                'label' => 'Active'
            ))*/
            ->add('active', 'boolean', array(
                'label' => 'Active'
            ))
            ->add('ip', 'text', array('label' => 'IP'))
            ->add('email', 'text', array('label' => 'Email'))
            ->add('phone', 'text', array('label' => 'Phone'))
            ->add('website', 'text', array('label' => 'Url'))
            ->add('adress', 'text', array('label' => 'Adress'))
            ->add('adressExtra', 'text', array('label' => 'Extra'))
            ->add('zipcode', 'number', array('label' => 'Zipcode'))
            ->add('city', 'text', array('label' => 'City'))
            ->add('createdAt', 'datetime', array(
                'required' => false
            ))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('username')
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('createdAt')
            ->add('lastLogin')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('firstName')
            ->add('lastName')
            ->add('createdAt')
            ->add('lastLogin')
        ;
    }
}