<?php

namespace PhpInk\Nami\CoreBundle\Form\Type\Category;

use PhpInk\Nami\CoreBundle\Form\Type\CategoryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use PhpInk\Nami\CoreBundle\Form\Type\BaseType;

class SortType extends BaseType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return FormBuilderInterface
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('elements', 'collection', array(
            'type' => new CategoryType(
                array('mapId' => true)
            ),
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'cascade_validation' => true,
            'mapped' => true
        ));
        $builder->add('initialized', 'checkbox', array('mapped' => false));
        $builder->add('count', 'number', array('mapped' => false));
        return $builder;
    }

    /**
     * @param OptionsResolverInterface $resolver
     * @return array
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'PhpInk\Nami\CoreBundle\Util\Collection',
                'csrf_protection' => false,
                'intention' => 'category_sort',
                'translation_domain' => 'NamiCoreBundle',
                //'allow_extra_fields' => true // Symfony 2.6
            )
        );
    }
}
