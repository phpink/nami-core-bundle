<?php

namespace PhpInk\Nami\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class UserResetType
 *
 * @package PhpInk\Nami\CoreBundle\Form\Type
 */
class UserResetType extends AbstractType
{
    /**
     * Form type building
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array                $options The form options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'plainPassword', 'repeated', array(
                'type' => 'password',
                'mapped' => 'plainPassword',
                'options' => array('translation_domain' => 'NamiCoreBundle'),
                'first_name' => 'new',
                'second_name' => 'confirmation',
                'invalid_message' => 'Password and confirmation does not match',
            )
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
        $resolver->setDefaults(
            array(
                'data_class' => $this->isORM ?
                    'PhpInk\Nami\CoreBundle\Model\Orm\User' :
                    'PhpInk\Nami\CoreBundle\Model\Odm\User',
                'intention' => 'resetting',
            )
        );
    }

    /**
     * Get the name of the form type.
     *
     * @return string The form type name.
     */
    public function getName()
    {
        // Empty string to map all fields at top level
        return '';
    }
}
