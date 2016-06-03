<?php

namespace PhpInk\Nami\CoreBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class BaseType
 *
 * @package PhpInk\Nami\CoreBundle\Form\Type
 */
class BaseType extends AbstractType
{
    /**
     * Is the form in edit mode ?
     *
     * @var bool
     */
    protected $isEdit = false;

    /**
     * Is the form a filter set for another form
     *
     * @var bool
     */
    protected $isFilter = false;

    /**
     * Is the Db ORM or Mongo ?
     *
     * @var bool
     */
    protected $isORM = true;

    /**
     * The user submitting the form
     *
     * @var User
     */
    protected $user;

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
        foreach ($this->fields as $fieldName => $field) {
            $fieldOptions = $this->isFilter ?
                array('required' => false) : array();

            // Default field options
            if (array_key_exists('options', $field) && is_array($field['options'])) {
                $fieldOptions = array_merge($field['options'], $fieldOptions);
            }

            if (array_key_exists('isAdmin', $field) && $field['isAdmin'] === true) {
                if (!$this->isAdmin()) {
                    continue;
                }
            }
            if ($field['type'] === 'model') {
                // Model field type
                $builder = $this->addModel(
                    $fieldName, $builder, $fieldOptions
                );
            } else if ($field['type'] === 'unmappedText') {
                // Unmapped field type
                if (!$this->isFilter) {
                    $builder->add(
                        $fieldName, 'text',
                        array_merge(
                            $fieldOptions,
                            array('mapped' => false)
                        )
                    );
                }
            } else {
                // Default field type
                $builder->add(
                    $fieldName,
                    $field['type'],
                    $fieldOptions
                );
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['isORM', 'isEdit', 'isFilter', 'user']);
        $resolver->addAllowedTypes('isORM', 'boolean');
        $resolver->addAllowedTypes('isEdit', 'boolean');
        $resolver->addAllowedTypes('isFilter', 'boolean');
        $resolver->addAllowedTypes('user', [UserInterface::class, 'NULL']);
    }

    public function getBaseOptions($options)
    {
        return [
          'isORM' => $options['isORM'],
          'isEdit' => $options['isEdit'],
          'isFilter' => $options['isFilter'],
          'user' => $options['user'],
        ];
    }

    /**
     * Adds a entity/document field to a form
     *
     * @param string               $field   Field name
     * @param FormBuilderInterface $builder The form builder.
     * @param array                $options Fields options
     *
     * @return FormBuilderInterface
     */
    public function addModel(
        $field, FormBuilderInterface $builder, $options = array()
    ) {
        $builder->add(
            $field,
            EntityType::class,//$this->isORM ? 'entity' : 'document',
            $options
        );
        return $builder;
    }

    /**
     * Adds a DateTime field to a form (ISO8601 string)
     *
     * @param string               $field   Field name
     * @param FormBuilderInterface $builder The form builder.
     * @param array                $options Fields options
     *
     * @return FormBuilderInterface
     */
    public function addDate(
        $field, FormBuilderInterface $builder, $options = array()
    ) {
        $builder->add(
            $field, DateTimeType::class, array_merge(
                $options, array(
                    'widget' => 'single_text',
                    'input' => 'datetime',
                    'required' => false
                )
            )
        );
        return $builder;
    }

    /**
     * Adds createdAt & updatedAt unmapped fields to a form
     *
     * @param FormBuilderInterface $builder The form builder.
     *
     * @return FormBuilderInterface
     */
    public function addCreatedUpdatedAt(FormBuilderInterface $builder)
    {
        $builder->add('createdAt', TextType::class, array('mapped' => false));
        $builder->add('updatedAt', TextType::class, array('mapped' => false));
        return $builder;
    }

    /**
     * Adds createdBy & updatedBy unmapped fields to a form
     *
     * @param FormBuilderInterface $builder The form builder.
     *
     * @return FormBuilderInterface
     */
    public function addCreatedUpdatedBy(FormBuilderInterface $builder)
    {
        $builder->add('createdBy', TextType::class, array('mapped' => false));
        $builder->add('updatedBy', TextType::class, array('mapped' => false));
        return $builder;
    }

    /**
     * Adds dateStart & dateEnd datetime fields to a form
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array                $options Fields options
     *
     * @return FormBuilderInterface
     */
    public function addProgrammableDates(
        FormBuilderInterface $builder, $options = array()
    ) {
        $builder->add(
            'dateStart', DateTimeType::class, array_merge(
                $options, array(
                    'widget' => 'single_text',
                    'input' => 'datetime',
                    'required' => false
                )
            )
        );
        $builder->add(
            'dateEnd', DateTimeType::class, array_merge(
                $options, array(
                    'widget' => 'single_text',
                    'input' => 'datetime',
                    'required' => false
                )
            )
        );
        return $builder;
    }

    /**
     * Get the name of the form type.
     *
     * @return string The form type name.
     */
    public function getBlockPrefix()
    {
        // Empty string to map all fields at top level
        return '';
    }
}
