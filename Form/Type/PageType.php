<?php

namespace PhpInk\Nami\CoreBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PageType
 *
 * @package PhpInk\Nami\CoreBundle\Form\Type
 */
class PageType extends BaseType
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
            'active' => array('type' => 'checkbox'),
            'title' => array('type' => 'text'),
            'header' => array('type' => 'text'),
            'slug' => array('type' => 'text'),
            'content' => array('type' => 'text'),
            'metaDescription' => array('type' => 'text'),
            'metaKeywords' => array('type' => 'text'),
            'blocks' => array(
                'type' => 'collection',
                'options' => array(
                    'type' => new BlockType($this->getOptions()),
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'cascade_validation' => true,
                    'mapped' => true
                )
            ),
            'background' => array(
                'type' => 'model',
                'options' => array(
                    'class' => 'NamiCoreBundle:Image',
                    'choice_label' => 'id',
                    'required' => false
                )
            ),
            'category' => array(
                'type' => 'model',
                'options' => array(
                    'class' => 'NamiCoreBundle:Category',
                    'choice_label' => 'id',
                    'required' => false
                )
            ),
            'template' => array(
                'type' => 'text',
                'options' => array(
                    'empty_data' => 'default'
                )
            ),
            'backgroundColor' => array('type' => 'text'),
            'footerColor' => array('type' => 'text'),
            'borderColor' => array('type' => 'text'),
            'negativeText' => array('type' => 'checkbox'),
            'createdAt' => array('type' => 'unmappedText'),
            'updatedAt' => array('type' => 'unmappedText'),
            'createdBy' => array('type' => 'unmappedText'),
            'updatedBy' => array('type' => 'unmappedText')
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
        $defaultOptions = array(
            'csrf_protection' => false,
            'translation_domain' => 'NamiCoreBundle'
        );
        if (!$this->isFilter) {
            $defaultOptions['data_class'] = $this->isORM ?
                'PhpInk\Nami\CoreBundle\Model\Orm\Page' :
                'PhpInk\Nami\CoreBundle\Model\Odm\Page';
            $defaultOptions['intention'] = 'page';
        }
        $resolver->setDefaults($defaultOptions);
    }
}
