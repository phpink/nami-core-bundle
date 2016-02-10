<?php

namespace PhpInk\Nami\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
     * FormType Constructor
     *
     * @param array $options Form type options.
     */
    public function __construct($options = array())
    {
        if (is_array($options)) {
            if (array_key_exists('isEdit', $options)) {
                $this->isEdit = ($options['isEdit'] === true);
            }
            if (array_key_exists('isFilter', $options)) {
                $this->isFilter = ($options['isFilter'] === true);
            }
            if (array_key_exists('isORM', $options)) {
                $this->isORM = ($options['isORM'] === true);
            }
            if (array_key_exists('user', $options)
                && $options['user'] instanceof UserInterface
            ) {
                $this->user = $options['user'];
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
            $this->isORM ? 'entity' : 'document',
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
            $field, 'datetime', array_merge(
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
        $builder->add('createdAt', 'text', array('mapped' => false));
        $builder->add('updatedAt', 'text', array('mapped' => false));
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
        $builder->add('createdBy', 'text', array('mapped' => false));
        $builder->add('updatedBy', 'text', array('mapped' => false));
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
            'dateStart', 'datetime', array_merge(
                $options, array(
                    'widget' => 'single_text',
                    'input' => 'datetime',
                    'required' => false
                )
            )
        );
        $builder->add(
            'dateEnd', 'datetime', array_merge(
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
    public function getName()
    {
        // Empty string to map all fields at top level
        return '';
    }

    /**
     * Is the form in edition mode (model update) ?
     *
     * @return bool
     */
    public function isEditMode()
    {
        return $this->isEdit;
    }

    /**
     * Is the Db manager is ORM or ODM ?
     *
     * @return bool
     */
    public function isORM()
    {
        return $this->isORM;
    }

    /**
     * Get the user making the request.
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Is the user has the admin role ?
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->user ?
            $this->user->isAdmin() : false;
    }

    /**
     * Get the form type options
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            'isEdit' => $this->isEditMode(),
            'isORM' => $this->isORM(),
            'user' => $this->getUser()
        );
    }
}
