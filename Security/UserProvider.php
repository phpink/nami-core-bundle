<?php

namespace PhpInk\Nami\CoreBundle\Security;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Doctrine\ODM\MongoDB\DocumentManager;
use PhpInk\Nami\CoreBundle\EventListener\UserSubscriber;
use PhpInk\Nami\CoreBundle\Model\UserInterface;
use PhpInk\Nami\CoreBundle\Exception\SubscriptionException;
use PhpInk\Nami\CoreBundle\Exception\InactiveAccountException;

class UserProvider implements UserProviderInterface
{
    /**
     * @var PasswordEncoderInterface|EncoderFactoryInterface
     */
    protected $encoder;

    /**
     * User repository
     */
    protected $userRepo;

    /**
     * @var string
     */
    protected $userClass;

    /**
     * Constructor.
     *
     * @param $encoder
     */
    public function __construct($encoder)
    {
        $this->encoder = $encoder;
    }

    public function setManager($em)
    {
        if ($em instanceof DocumentManager) {
            $this->userClass = 'PhpInk\Nami\CoreBundle\Model\Odm\User';
        } else {
            $this->userClass = 'PhpInk\Nami\CoreBundle\Model\Orm\User';
        }
        $this->userRepo = $em->getRepository('NamiCoreBundle:User');
        // Register a UserListener to update login info
        $em->getEventManager()->addEventListener(
            array(
                'prePersist', 'preUpdate'
            ),
            new UserSubscriber($this)
        );
    }

    /**
     * Returns an empty user instance
     *
     * @return UserInterface
     */
    public function createUser()
    {
        $class = $this->userClass;
        $user = new $class;
        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        $user = $this->findUser($username);
        if (!$user instanceof AdvancedUserInterface) {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $username)
            );

        } elseif ($user->isLocked()) {
            throw new InactiveAccountException('The account has been locked.');

        } elseif (!$user->isActive()) {
            throw new InactiveAccountException();

        }
        return $user;
    }

    /**
     * Finds a user by username.
     *
     * @param string $username
     *
     * @return UserInterface|null
     */
    protected function findUser($username)
    {
        return $this->findUserByUsernameOrEmail($username);
    }

    /**
     * Update user instance.
     *
     * @param UserInterface $user
     *
     * @return UserInterface|null
     */
    public function updateUser($user)
    {
        return $this->userRepo->saveEntity($user);
    }

    /**
     * Finds a user either by email, or username
     *
     * @param string $usernameOrEmail
     *
     * @return UserInterface
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        return $this->userRepo->findUserByUsernameOrEmail($usernameOrEmail);
    }

    /**
     * Finds a user either by confirmation token
     *
     * @param string $token
     *
     * @return UserInterface
     */
    public function findUserByConfirmationToken($token)
    {
        return $this->userRepo->findUserByConfirmationToken($token);
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(SecurityUserInterface $user)
    {
        if (!$user instanceof UserInterface) {
            throw new UnsupportedUserException(
                sprintf(
                    'Expected an instance of '.
                    'PhpInk\Nami\CoreBundle\Model\UserInterface, but got "%s".',
                    get_class($user)
                )
            );
        }

        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(
                sprintf(
                    'Expected an instance of %s, but got "%s".',
                    $this->userManager->getClass(), get_class($user)
                )
            );
        }

        if (null === $reloadedUser = $this->userRepo->findUserById($user->getId())) {
            throw new UsernameNotFoundException(
                sprintf('User with ID "%d" could not be reloaded.', $user->getId())
            );
        }

        return $reloadedUser;
    }

    protected function getEncodedPassword(UserInterface $user, $password)
    {
        return (!$this->encoder instanceof PasswordEncoderInterface) ?
            $this->encoder->encodePassword($user, $password, $user->getSalt()) :
            $this->encoder->getEncoder($user)->encodePassword($password, $user->getSalt());
    }

    /**
     * {@inheritDoc}
     */
    public function updateFields(UserInterface $user)
    {
        if (0 !== strlen($password = $user->getPlainPassword())) {
            $user->setPassword(
                $this->getEncodedPassword($user, $password)
            );
            $user->eraseCredentials();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $this->userClass === $class
        || is_subclass_of($class, $this->userClass);
    }
}
