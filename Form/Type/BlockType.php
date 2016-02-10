<?php

namespace PhpInk\Nami\CoreBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class BlockType
 *
 * @package PhpInk\Nami\CoreBundle\Form\Type
 */
class BlockType extends BaseType
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
        $builder->add('id', 'text');
        $builder->add('active', 'checkbox');
        $builder->add('position', 'integer');
        $builder->add('title', 'text');
        $builder->add('content', 'text');
        $builder->add('position', 'integer');
        $builder = $this->addModel(
            'page', $builder, array(
                'class' => 'NamiCoreBundle:Page',
                'property' => 'id',
                'required' => false
            )
        );
        $builder->add('template', 'text', array('empty_data' => 'default'));
        $builder->add('type', 'text');
        $builder = $this->addModel(
            'images', $builder, array(
                'multiple' => true,
                'class' => 'NamiCoreBundle:Image',
                'property' => 'id',
                'required' => false
            )
        );
        $builder->add('uuid', 'text', array('mapped' => false));
        $builder = $this->addCreatedUpdatedAt($builder);
        $builder = $this->addCreatedUpdatedBy($builder);
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
                    'PhpInk\Nami\CoreBundle\Model\Orm\Block' :
                    'PhpInk\Nami\CoreBundle\Model\Odm\Block',
                'csrf_protection' => false,
                'intention' => 'block',
                'translation_domain' => 'NamiCoreBundle'
            )
        );
    }
}
