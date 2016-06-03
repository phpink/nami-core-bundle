<?php

namespace PhpInk\Nami\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
     * @param OptionsResolver $resolver The resolver.
     *
     * @return array
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver); 
        $resolver->setDefault('data_class', function (Options $options) {
            return ($options['isORM']) ?
                'PhpInk\Nami\CoreBundle\Model\Orm\User' :
                'PhpInk\Nami\CoreBundle\Model\Odm\User';
       
         });
        $resolver->setDefaults('intention', 'resetting');
    }
}
