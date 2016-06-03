<?php

namespace PhpInk\Nami\CoreBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ImageType
 *
 * @package PhpInk\Nami\CoreBundle\Form\Type
 */
class ImageType extends BaseType
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
        $builder->add('name', TextType::class, ['required' => true]);
        $builder->add('file', FileType::class, ['required' => true]);
        if ($options['imageType'] === 'BlockImage') {
            $builder->add('position', IntegerType::class, ['required' => true]);
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
        $resolver->setRequired(['imageType']);
        $resolver->addAllowedTypes('imageType', 'string');
        $resolver->setDefaults([
            'csrf_protection' => false,
            'intention' => 'image',
            'translation_domain' => 'NamiCoreBundle'
        ]);
        $resolver->setDefault('data_class', function (Options $options) {
            return ($options['isORM']) ?
                'PhpInk\Nami\CoreBundle\Model\Orm\Image\\'. $options['imageType'] :
                'PhpInk\Nami\CoreBundle\Model\Odm\Image\\'. $options['imageType'];

        });
    }
}
