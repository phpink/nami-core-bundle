<?php

namespace PhpInk\Nami\CoreBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class UserType
 *
 * @package PhpInk\Nami\CoreBundle\Form\Type4
 */
class UserType extends BaseType
{
    /**
     * FormType Constructor
     *
     * @param array $options Form type options.
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->fields = array(
            'id' => array('type' => 'unmappedText'),
            'username' => array(
                'type' => 'text',
                'options' => array('required' => true)
            ),
            'password' => array(
                'type' => 'repeated',
                'options' => array(
                    'type' => 'password',
                    'mapped' => 'plainPassword',
                    'options' => array('translation_domain' => 'NamiCoreBundle'),
                    'first_name' => 'value',
                    'second_name' => 'confirmation',
                    'invalid_message' => 'Password and confirmation does not match'
                )
            ),
            'lastLogin' => array('type' => 'unmappedText'),
            'firstName' => array(
                'type' => 'text',
                'options' => array('required' => true)
            ),
            'lastName' => array(
                'type' => 'text',
                'options' => array('required' => true)
            ),
            'male' => array('type' => 'checkbox'),
            'avatar' => array(
                'type' => 'model',
                'options' => array(
                    'class' => 'NamiCoreBundle:Image',
                    'property' => 'id',
                    'required' => false
                )
            ),
            'email' => array('type' => 'text'),
            'phone' => array('type' => 'text'),
            'address' => array('type' => 'text'),
            'addressExtra' => array('type' => 'text'),
            'zipcode' => array('type' => 'integer'),
            'city' => array('type' => 'text'),
            'website' => array('type' => 'text'),
            'presentation' => array('type' => 'text'),

            'active' => array('type' => 'checkbox', 'isAdmin' => true),
            'ip' => array('type' => 'unmappedText', 'isAdmin' => true),
            'locked' => array('type' => 'checkbox', 'isAdmin' => true),
            'roles' => array(
                'type' => 'collection',
                'options' => array('type' => 'text'),
                'isAdmin' => true
            ),

            'createdAt' => array('type' => 'unmappedText'),
            'updatedAt' => array('type' => 'unmappedText')
        );
    }

    /**
     * Form type default options
     *
     * @param OptionsResolverInterface $resolver The resolver.
     *
     * @return array
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $validationGroups = array('Default');
        $validationGroups[] = $this->isEditMode() ?
            'profile' : 'registration';

        $defaultOptions = array(
            'csrf_protection' => false,
            'cascade_validation' => true,
            'translation_domain' => 'NamiCoreBundle',
            'validation_groups' => $validationGroups
        );
        if (!$this->isFilter) {
            $defaultOptions['data_class'] = $this->isORM ?
                'PhpInk\Nami\CoreBundle\Model\Orm\User' :
                'PhpInk\Nami\CoreBundle\Model\Odm\User';
            $defaultOptions['intention'] = 'user';
        }
        $resolver->setDefaults($defaultOptions);
    }
}
