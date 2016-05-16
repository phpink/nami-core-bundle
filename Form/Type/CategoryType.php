<?php

namespace PhpInk\Nami\CoreBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CategoryType
 *
 * @package PhpInk\Nami\CoreBundle\Form\Type
 */
class CategoryType extends BaseType
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
        $builder->add('id', TextType::class);
        $builder->add('active', CheckboxType::class);
        $builder->add('name', TextType::class);
        $builder->add('title', TextType::class);
        $builder->add('slug', TextType::class);
        $builder->add('parent', EntityType::class, [
            'class' => 'NamiCoreBundle:Category',
            'choice_label' => 'id',
            'required' => false
        ]);
        $builder->add('header', TextType::class);
        $builder->add('metaDescription', TextType::class);
        $builder->add('metaKeywords', TextType::class);
        $builder->add('content', TextType::class);

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
            return ($options['isORM']) ?
                'PhpInk\Nami\CoreBundle\Model\Orm\Category' :
                'PhpInk\Nami\CoreBundle\Model\Odm\Category';

        });
        $resolver->setDefault('intention', function (Options $options, $previousValue) {
            return ($options['isFilter']) ? $previousValue : 'category';
        });
    }
}
