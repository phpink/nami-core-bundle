<?php

namespace PhpInk\Nami\CoreBundle\Admin\Orm;

use PhpInk\Nami\CoreBundle\Model\Orm\Category;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ImageAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        // get the current Image instance
        $image = $this->getSubject();

        // use $fileFieldOptions so we can add other options to the field
        $fileFieldOptions =  array(
            'label' => 'File',
            'required' => false,
        );
        if ($image && ($webPath = $image->getUrl())) {
            // add a 'help' option containing the preview's img
            $container = $this->getConfigurationPool()->getContainer();
            $imageUrl = $container->get('liip_imagine.cache.manager')->getBrowserPath($image->getUrl(false), 'preview');
            $fileFieldOptions['help'] = '<img src="'.$imageUrl.'" class="admin-preview" />';
        }
        $formMapper
            ->add('file', 'file', $fileFieldOptions)
            ->add('folder', 'text', array(
                'label' => 'Folder',
            ))
            ->add('filename', 'text', array(
                'label' => 'Filename',
            ))
        ;
    }

    public function prePersist($image)
    {
        $this->manageFileUpload($image);
    }

    public function preUpdate($image)
    {
        $this->manageFileUpload($image);
    }

    private function manageFileUpload($image)
    {
        if ($image->getFile()) {
            $image->refreshUpdated();
        }
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('folder')
            ->add('createdBy')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('folder')
            ->add('createdAt');
    }

    public function toString($object)
    {
        return $object instanceof Category
            ? $object->getName()
            : 'Image'; // shown in the breadcrumb on the create view
    }
}