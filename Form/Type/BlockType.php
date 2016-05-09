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
        $builder->add('id', TextType::class);
        $builder->add('active', CheckboxType::class);
        $builder->add('position', IntegerType::class);
        $builder->add('title', TextType::class);
        $builder->add('content', TextType::class);
        $builder->add('position', IntegerType::class);
        $builder->add('page', EntityType::class, [
            'class' => 'NamiCoreBundle:Page',
            'choice_label' => 'id',
            'required' => false
        ]);
        $builder->add('template', TextType::class, array('empty_data' => 'default'));
        $builder->add('type', TextType::class);
        $builder->add('images', EntityType::class, [
            'class' => 'NamiCoreBundle:Image',
            'multiple' => true,
            'choice_label' => 'id',
            'choices_as_values' => true,
            'required' => false
        ]);
        $builder->add('uuid', TextType::class, ['mapped' => false]);
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
            'intention' => 'block',
            'translation_domain' => 'NamiCoreBundle'
        ]);
        $resolver->setDefault('data_class', function (Options $options) {
            return ($options['isORM']) ?
                'PhpInk\Nami\CoreBundle\Model\Orm\Block' :
                'PhpInk\Nami\CoreBundle\Model\Odm\Block';

        });
    }
}
