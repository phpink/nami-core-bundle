<?php

namespace PhpInk\Nami\CoreBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ImageType
 *
 * @package PhpInk\Nami\CoreBundle\Form\Type
 */
class ImageType extends BaseType
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
        $builder->add('id', 'text', array('mapped' => false));
        $builder->add('name', 'text', array('required' => true));
        $builder->add('file', 'file', array('required' => true));
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
        $resolver->setDefaults(
            array(
                'data_class' => $this->isORM ?
                    'PhpInk\Nami\CoreBundle\Model\Orm\Image' :
                    'PhpInk\Nami\CoreBundle\Model\Odm\Image',
                'csrf_protection' => false,
                'intention' => 'image',
                'translation_domain' => 'NamiCoreBundle'
            )
        );
    }
}
