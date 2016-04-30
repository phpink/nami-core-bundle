<?php

namespace PhpInk\Nami\CoreBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            'id' => array('type' => TextType::class, ['mapped' => false]),
            'active' => array('type' => CheckboxType::class),
            'title' => array('type' => TextType::class),
            'header' => array('type' => TextType::class),
            'slug' => array('type' => TextType::class),
            'content' => array('type' => TextType::class),
            'metaDescription' => array('type' => TextType::class),
            'metaKeywords' => array('type' => TextType::class),
            'blocks' => array(
                'type' => CollectionType::class,
                'options' => array(
                    'entry_type' => BlockType::class,//new BlockType($this->getOptions()),
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    //'cascade_validation' => true,
                    'mapped' => true
                )
            ),
            'background' => array(
                'type' => EntityType::class,
                'options' => array(
                    'class' => 'NamiCoreBundle:Image',
                    'choice_label' => 'id',
                    'required' => false
                )
            ),
            'category' => array(
                'type' => EntityType::class,
                'options' => array(
                    'class' => 'NamiCoreBundle:Category',
                    'choice_label' => 'id',
                    'required' => false
                )
            ),
            'template' => array(
                'type' => TextType::class,
                'options' => array(
                    'empty_data' => 'default'
                )
            ),
            'backgroundColor' => array('type' => TextType::class),
            'footerColor' => array('type' => TextType::class),
            'borderColor' => array('type' => TextType::class),
            'negativeText' => array('type' => CheckboxType::class),
            'createdAt' => array('type' => TextType::class, ['mapped' => false]),
            'updatedAt' => array('type' => TextType::class, ['mapped' => false]),
            'createdBy' => array('type' => TextType::class, ['mapped' => false]),
            'updatedBy' => array('type' => TextType::class, ['mapped' => false])
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
