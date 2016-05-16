<?php

namespace PhpInk\Nami\CoreBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PageType
 *
 * @package PhpInk\Nami\CoreBundle\Form\Type
 */
class PageType extends BaseType
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
        $builder->add('id', TextType::class, ['mapped' => false]);
        $builder->add('active', CheckboxType::class);
        $builder->add('title', TextType::class);
        $builder->add('slug', TextType::class);
        $builder->add('header', TextType::class);
        $builder->add('metaDescription', TextType::class);
        $builder->add('metaKeywords', TextType::class);
        $builder->add('content', TextType::class);
        $builder->add('blocks',  CollectionType::class, [
            'entry_type' => BlockType::class,
            'entry_options' => $this->getBaseOptions($options),
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            //'cascade_validation' => true,
            'mapped' => true
        ]);
        $builder->add('category', EntityType::class, [
            'class' => 'NamiCoreBundle:Category',
            'choice_label' => 'id',
            'required' => false
        ]);
        $builder->add('background', EntityType::class, [
            'class' => 'NamiCoreBundle:Image\Background',
            'choice_label' => 'id',
            'required' => false
        ]);
        $builder->add('template', TextType::class, [
            'empty_data' => 'default'
        ]);
        $builder->add('backgroundColor', TextType::class);
        $builder->add('footerColor', TextType::class);
        $builder->add('borderColor', TextType::class);
        $builder->add('negativeText', CheckboxType::class);
        
        $this->addCreatedUpdatedAt($builder);
        $this->addCreatedUpdatedBy($builder);
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
        $resolver->setDefaults([
            'csrf_protection' => false,
            'translation_domain' => 'NamiCoreBundle'
        ]);
        $resolver->setDefault('data_class', function (Options $options) {
            return ($options['isFilter']) ?
                null : ($options['isORM']) ?
                    'PhpInk\Nami\CoreBundle\Model\Orm\Page' :
                    'PhpInk\Nami\CoreBundle\Model\Odm\Page';

        });
        $resolver->setDefault('intention', function (Options $options, $previousValue) {
            return ($options['isFilter']) ? $previousValue : 'page';
        });
    }
}
