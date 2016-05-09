<?php

namespace PhpInk\Nami\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BulkType
 *
 * @package PhpInk\Nami\CoreBundle\Form\Type
 */
class BulkType extends BaseType
{
    /**
     * Model name
     * @var string
     */
    protected $model;

    /**
     * Model FormType class name
     * @var string|false
     */
    protected $modelType;

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
        $builder = $this->addModel(
            'id', $builder, array(
                'multiple' => true,
                'class' => 'NamiCoreBundle:'. $options['model'],
                'choice_label' => 'id',
                'required' => true
            )
        );
        if ($this->modelType !== false) {
            $builder->add(
                'fields', $options['modelType'],
                array_merge(
                    $options, [
                        'isFilter' => true
                    ]
                )
            );
        }
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
        $resolver->setRequired(['model', 'modelType']);
        $resolver->addAllowedTypes('model', 'string');
        $resolver->addAllowedTypes('modelType', 'string');
        $resolver->setDefaults(
            array(
                'csrf_protection' => false,
                'intention' => 'bulk',
                'translation_domain' => 'NamiCoreBundle',
                'compound' => true
            )
        );
    }
}
