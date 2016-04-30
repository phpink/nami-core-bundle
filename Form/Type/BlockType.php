<?php

namespace PhpInk\Nami\CoreBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
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
        $builder = $this->addModel(
            'page', $builder, array(
                'class' => 'NamiCoreBundle:Page',
                'choice_label' => 'id',
                'required' => false
            )
        );
        $builder->add('template', TextType::class, array('empty_data' => 'default'));
        $builder->add('type', TextType::class);
        $builder = $this->addModel(
            'images', $builder, array(
                'multiple' => true,
                'class' => 'NamiCoreBundle:Image',
                'choices_as_values' => true,
                'choice_label' => 'id',
                'required' => false
            )
        );
        $builder->add('uuid', TextType::class, ['mapped' => false]);
        $builder = $this->addCreatedUpdatedAt($builder);
        $builder = $this->addCreatedUpdatedBy($builder);
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
                    'PhpInk\Nami\CoreBundle\Model\Orm\Block' :
                    'PhpInk\Nami\CoreBundle\Model\Odm\Block',
                'csrf_protection' => false,
                'intention' => 'block',
                'translation_domain' => 'NamiCoreBundle'
            )
        );
    }
}
