<?php

namespace PhpInk\Nami\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use PhpInk\Nami\CoreBundle\Model\BlockInterface;
use PhpInk\Nami\CoreBundle\Model\ImageInterface;
use PhpInk\Nami\CoreBundle\Model\ModelInterface;
use PhpInk\Nami\CoreBundle\Model\PageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Rest controller for images
 *
 * @Annotations\NamePrefix("nami_api_")
 *
 * @package PhpInk\Nami\CoreBundle\Controller
 * @author  Geoffroy Pierret <geofrwa@yandex.com>
 */
class ImageController extends AbstractController
{
    /**
     * Image upload
     *
     * @param Request $request The request object.
     *
     * @return FormTypeInterface|View
     *
     * @Annotations\Post("/upload")
     * @ApiDoc(
     *   description = "Upload an image",
     *   input = "PhpInk\Nami\CoreBundle\Form\Type\ImageType",
     *   output = "ImageInterface",
     *   resource = false,
     *   statusCodes = {
     *     200 = "Returned when upload successful",
     *     401 = "Returned when upload failed"
     *   },
     *  parameters={
     *      {
     *        "name"="name", "dataType"="string",
     *        "required"=true, "description"="The name of the file."
     *      },
     *      {
     *         "name"="file", "dataType"="string",
     *         "required"=true, "description"="The binary content."
     *      },
     *      {
     *         "name"="folder", "dataType"="string",
     *         "required"=false, "description"="The upload sub-folder."
     *      }
     *  }
     * )
     */
    public function postUploadAction(Request $request)
    {
        /**
         * Image model
         * @var Image $image
         */
        $image = $this->getRepository()->createModel();
        // Validate subfolder
        $folder = $request->request->get('folder');
        if (in_array($folder, array('avatar', 'block', 'background'))) {
            $image->setFolder($folder);
        }
        return $this->processForm(
            $request, $image
        );
    }

    /**
     * Image upload for page (background)
     *
     * @param Request $request The request object.
     * @param integer $id      The page related to the image.
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when page does not exist
     *
     * @Annotations\Post("/pages/{id}/upload")
     * @ApiDoc(
     *   description = "Upload a page background",
     *   input = "PhpInk\Nami\CoreBundle\Form\Type\ImageType",
     *   output = "ImageInterface",
     *   resource = false,
     *   statusCodes = {
     *     200 = "Returned when upload successful",
     *     401 = "Returned when upload failed"
     *   },
     *  parameters={
     *      {
     *        "name"="name", "dataType"="string",
     *        "required"=true, "description"="The name of the file."
     *      },
     *      {
     *         "name"="file", "dataType"="string",
     *         "required"=true, "description"="The binary content."
     *      }
     *  }
     * )
     */
    public function postPagesUploadAction(Request $request, $id)
    {
        // Retrieve the brand
        $page = $this->getModelById($id, 'Page');
        $imageView = null;
        if ($page instanceof PageInterface) {
            $this->checkUserAccess('upload', $page);
            /**
             * Image model
             * @var Image $image
             */
            $image = $this->getRepository()->createModel();
            $image->setFolder('background');
            $imageView = $this->processForm(
                $request, $image
            );
            // If upload has been successfull
            if ($imageView->getStatusCode() === Codes::HTTP_CREATED) {
                $page->setBackground($image);
                $this->saveModel($page, Codes::HTTP_OK);
            }
        }
        return $imageView;
    }

    /**
     * Image upload for blocks
     *
     * @param Request $request The request object.
     * @param integer $id      The block related to the image.
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when block does not exist
     *
     * @Annotations\Post("/blocks/{id}/upload")
     * @ApiDoc(
     *   description = "Upload a block image",
     *   input = "PhpInk\Nami\CoreBundle\Form\Type\ImageType",
     *   output = "ImageInterface",
     *   resource = false,
     *   statusCodes = {
     *     200 = "Returned when upload successful",
     *     401 = "Returned when upload failed"
     *   },
     *  parameters={
     *      {
     *        "name"="name", "dataType"="string",
     *        "required"=true, "description"="The name of the file."
     *      },
     *      {
     *         "name"="file", "dataType"="string",
     *         "required"=true, "description"="The binary content."
     *      }
     *  }
     * )
     */
    public function postBlocksUploadAction(Request $request, $id)
    {
        // Retrieve the brand
        $block = $this->getModelById($id, 'Block');
        $imageView = null;
        if ($block instanceof BlockInterface) {
            $this->checkUserAccess('upload', $block);
            /**
             * Image model
             * @var Image $image
             */
            $image = $this->getRepository()->createModel();
            $image->setFolder('block');
            $imageView = $this->processForm(
                $request, $image
            );
            // If upload has been successfull
            if ($imageView->getStatusCode() === Codes::HTTP_CREATED) {
                $block->addImage($image);
                $this->saveModel($block, Codes::HTTP_OK);
            }
        }
        return $imageView;
    }

    /**
     * Image upload for users (avatars)
     *
     * @param Request $request The request object.
     * @param integer $id      The user related to the image.
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when user does not exist
     *
     * @Annotations\Post("/users/{id}/upload")
     * @ApiDoc(
     *   description = "Upload a user avatar",
     *   input = "PhpInk\Nami\CoreBundle\Form\Type\ImageType",
     *   output = "ImageInterface",
     *   resource = false,
     *   statusCodes = {
     *     200 = "Returned when upload successful",
     *     401 = "Returned when upload failed"
     *   },
     *  parameters={
     *      {
     *        "name"="name", "dataType"="string",
     *        "required"=true, "description"="The name of the file."
     *      },
     *      {
     *         "name"="file", "dataType"="string",
     *         "required"=true, "description"="The binary content."
     *      }
     *  }
     * )
     */
    public function postUsersUploadAction(Request $request, $id)
    {
        // Retrieve the brand
        $user = $this->getModelById($id, 'User');
        $imageView = null;
        if ($user instanceof UserInterface) {
            $this->checkUserAccess('upload', $user);
            /**
             * Image model
             * @var Image $image
             */
            $image = $this->getRepository()->createModel();
            $image->setFolder('avatar');
            $imageView = $this->processForm(
                $request, $image
            );
            // If upload has been successfull
            if ($imageView->getStatusCode() === Codes::HTTP_CREATED) {
                $user->setAvatar($image);
                $this->saveModel($user, Codes::HTTP_OK);
            }
        }
        return $imageView;
    }

    /**
     * Triggered on upload form preSave
     * Uploads the file
     *
     * @param ModelInterface $image   The image model to be saved.
     * @param Request        $request The request object.
     *
     * @return void
     */
    protected function onPreSave($image, Request $request = null)
    {
        if ($image instanceof ImageInterface) {
            $image->upload();
        }

    }

    /**
     * {@inheritDoc}
     */
    protected function checkUserAccess($type = 'upload', ModelInterface $entity = null)
    {
        switch ($type) {
            default:
                // If not manager and admin,
                // ALLOW self/edited user
                if (!$this->isAdmin()) {
                    /*if ($manager = $entity->getCreatedBy()) {
                        $this->checkIsLoggedUser($manager);
                    } else {*/
                        $this->throwAccessDenied();
                    //}

                }
                break;
        }
    }
}
