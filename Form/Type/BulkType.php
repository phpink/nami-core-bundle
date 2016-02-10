<?php

namespace PhpInk\Nami\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
     * FormType Constructor
     *
     * @param array $options Form type options.
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
        if (is_array($options)) {
            if (array_key_exists('model', $options)) {
                $this->model = $options['model'];
            }
            if (array_key_exists('modelType', $options)) {
                $this->modelType = $options['modelType'];
            }
        }
    }

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
                'class' => 'NamiCoreBundle:'. $this->model,
                'property' => 'id',
                'required' => true
            )
        );
        if ($this->modelType !== false) {
            $builder->add(
                'fields', new $this->modelType(
                    array_merge(
                        $this->getOptions(),
                        array(
                            'isFilter' => true
                        )
                    )
                )
            );
        }
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
                'csrf_protection' => false,
                'intention' => 'bulk',
                'translation_domain' => 'NamiCoreBundle',
                'compound' => true
            )
        );
    }
}
