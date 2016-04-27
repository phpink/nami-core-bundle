<?php

namespace PhpInk\Nami\CoreBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CategoryType
 *
 * @package PhpInk\Nami\CoreBundle\Form\Type
 */
class CategoryType extends BaseType
{
    /**
     * FormType Constructor
     *
     * @param array $options Form type options.
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->depth = 0;
        $this->mapId = false;
        if (is_array($options)) {
            if (array_key_exists('depth', $options)) {
                $this->depth = $options['depth'];
            }
            if (array_key_exists('mapId', $options)) {
                $this->mapId = boolval($options['mapId']);
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
        $builder->add('id', 'text', array('mapped' => $this->mapId));
        $builder->add('active', 'checkbox');
        $builder->add('name', 'text');
        $builder->add('position', 'integer');
        $builder = $this->addModel(
            'parent', $builder, array(
                'class' => 'NamiCoreBundle:Category',
                'choice_label' => 'id',
                'required' => false
            )
        );
        $builder->add('header', 'text');
        $builder->add('metaDescription', 'text');
        $builder->add('metaKeywords', 'text');
        $builder->add('content', 'text');
        if ($this->depth < 2) { // Limit the tree to 2 levels
            $builder->add(
                'items', 'collection', array(
                    'type' => new self(
                        array(
                            'depth' => $this->depth + 1,
                            'mapId' => $this->mapId
                        )
                    ),
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'cascade_validation' => true
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
        $resolver->setDefaults(
            array(
                'data_class' => $this->isORM ?
                    'PhpInk\Nami\CoreBundle\Model\Orm\Category' :
                    'PhpInk\Nami\CoreBundle\Model\Odm\Category',
                'csrf_protection' => false,
                'intention' => 'category',
                'translation_domain' => 'NamiCoreBundle'
            )
        );
    }
}
