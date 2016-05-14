<?php

namespace PhpInk\Nami\CoreBundle\Model\Orm;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Hateoas\Configuration\Annotation as Hateoas;
use PhpInk\Nami\CoreBundle\Model\Orm\Core;
use PhpInk\Nami\CoreBundle\Model\Orm\Image\UserImage;
use PhpInk\Nami\CoreBundle\Model\Image\UserImageInterface;
use PhpInk\Nami\CoreBundle\Model\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="PhpInk\Nami\CoreBundle\Repository\Orm\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("custom", custom = {
 *     "id", "username", "firstName", "lastName",
 *     "male", "active", "locked",
 *     "lastLogin", "ip", "email",
 *     "phone", "country",
 *     "createdAt", "updatedAt",
 *     "_references"
 * })
 * @Hateoas\Relation(
 *   "self",
 *   href = @Hateoas\Route(
 *     "nami_api_get_user",
 *     parameters = {"id" = "expr(object.getId())"}
 *   )
 * )
 */
class User extends Core\Entity implements AdvancedUserInterface,UserInterface
{
    use Core\CreatedUpdatedAtTrait;

    /**
     * Available User roles
     * @var string
     */
    const ROLE_DEFAULT = 'ROLE_USER';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * Primary Key
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose
     */
    protected $username;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     * @JMS\Expose
     * @JMS\Groups({"full"})
     */
    protected $active;

    /**
     * The salt to use for hashing
     *
     * @var string
     * @ORM\Column(type="string")
     */
    protected $salt;

    /**
     * Plain password.
     * Used for validation. Must not be persisted.
     *
     * @var string
     */
    protected $plainPassword;

    /**
     * Encrypted password. Must be persisted.
     *
     * @var string
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     * @JMS\Expose
     */
    protected $firstName;

    /**
     * @var string
     * @ORM\Column(name="last_name", type="string", length=255)
     * @JMS\Expose
     */
    protected $lastName;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     * @JMS\Expose
     */
    protected $male;

    /**
     * @var \DateTime
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     * @JMS\Expose
     */
    protected $lastLogin;

    /**
     * @var string
     * @ORM\Column(name="ip", type="string", length=39, nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"full"})
     */
    protected $ip;

    /**
     * Random string sent to the user
     * email address in order to verify it
     *
     * @var string
     * @ORM\Column(name="confirmation_token", type="string", nullable=true)
     */
    protected $confirmationToken;

    /**
     * @var \DateTime
     * @ORM\Column(name="password_requested_at", type="datetime", nullable=true)
     */
    protected $passwordRequestedAt;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     * @JMS\Expose
     * @JMS\Groups({"full"})
     */
    protected $locked;

    /**
     * @var array<string>
     * @ORM\Column(type="array")
     * @JMS\Expose
     * @JMS\Groups({"full"})
     */
    protected $roles;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255)
     * @JMS\Expose
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    protected $phone;

    /**
     * @var UserImage
     * @ORM\OneToOne(targetEntity="PhpInk\Nami\CoreBundle\Model\Orm\Image\UserImage", mappedBy="user", cascade={"persist", "remove"})
     * @JMS\Expose
     * @JMS\Type("integer")
     * @JMS\Accessor("getAvatarId")
     */
    protected $avatar;

    /**
     * @var string
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    protected $address;

    /**
     * @var string
     * @ORM\Column(name="address_extra", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    protected $addressExtra;

    /**
     * @var string
     * @ORM\Column(name="zipcode", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    protected $zipcode;

    /**
     * @var string
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    protected $city;

    /**
     * @var string
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    protected $website;

    /**
     * @var string
     * @ORM\Column(name="presentation", type="text", nullable=true)
     * @JMS\Expose
     */
    protected $presentation;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(
     *     targetEntity="PhpInk\Nami\CoreBundle\Model\Orm\Analytics\LoginAnalytics",
     *     mappedBy="user",
     *     orphanRemoval=true,
     *     cascade={"remove"}
     * )
     */
    protected  $loginAnalytics;

    /**
     * @var array
     * @JMS\Expose
     * @JMS\Accessor("getReferences")
     * @JMS\MaxDepth(3)
     */
    protected $_references = array();

    /**
     * User constructor
     */
    public function __construct()
    {
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->active = false;
        $this->locked = false;
        $this->roles = array();
        $this->loginAnalytics = new ArrayCollection();
    }

    /**
     * Get the value of id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id.
     *
     * @param integer
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set the value of username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of username.
     *
     * @param string $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = trim($username);

        return $this;
    }


    /**
     * Set the value of salt.
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set the value of salt.
     *
     * @param string $salt
     * @return $this
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Gets the encrypted password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the encrypted password.
     *
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set the plain password.
     *
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set the plain password.
     *
     * @param string $password
     * @return $this
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;

        return $this;
    }

    /**
     * Sets the user ip
     *
     * @param string $ip
     * @return $this
     */
    public function setIp($ip)
    {
        return $this->ip = $ip;
    }

    /**
     * Gets the user ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Removes sensitive data from the user.
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * Gets the last login time.
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Sets the last login time.
     *
     * @return \DateTime|null $time
     * @return $this
     */
    public function setLastLogin(\DateTime $time = null)
    {
        $this->lastLogin = $time;

        return $this;
    }


    /**
     * Gets the value of confirmationToken.
     *
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * Sets the value of confirmationToken.
     *
     * @param string
     * @return $this
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isAccountNonExpired()
    {
        return $this->isEnabled();
    }

    /**
     * {@inheritDoc}
     */
    public function isAccountNonLocked()
    {
        return !$this->locked;
    }


    /**
     * {@inheritDoc}
     */
    public function isCredentialsNonExpired()
    {
        return !$this->isLocked();
    }

    /**
     * @param boolean $active
     *
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = trim($firstName);

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = trim($lastName);

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Gets the value of locked.
     *
     * @return bool
     */
    public function isLocked()
    {
        return !$this->isAccountNonLocked();
    }


    /**
     * Sets the value of locked.
     *
     * @param bool
     * @return $this
     */
    public function setLocked($boolean)
    {
        $this->locked = $boolean;

        return $this;
    }

    /**
     * Set the value for passwordRequestedAt.
     *
     * @param \DateTime
     * @return $this
     */
    public function setPasswordRequestedAt(\DateTime $date = null)
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    /**
     * Gets the timestamp that the user requested a password reset.
     *
     * @return null|\DateTime
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /**
     * {@inheritDoc}
     */
    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
        $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    /**
     * Set male
     *
     * @param boolean $male
     * @return $this
     */
    public function setMale($male)
    {
        $this->male = $male;

        return $this;
    }

    /**
     * Get male
     *
     * @return boolean
     */
    public function isMale()
    {
        return $this->male;
    }

    /**
     * Has role(s) methods
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole(static::ROLE_ADMIN) || $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    /**
     * Sets the ADMIN ROLE.
     *
     * @param $boolean
     * @return $this
     */
    public function setAdmin($boolean)
    {
        if (true === $boolean) {
            $this->addRole(static::ROLE_ADMIN);
        } else {
            $this->removeRole(static::ROLE_ADMIN);
        }
        return $this;
    }

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AuthorizationCheckerInterface
     *
     * @param string $role
     *
     * @return boolean
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * Remove a user role
     *
     * @param string $role The role
     * @return User
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * Add a user role
     *
     * @param string $role The role
     * @return User
     */
    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }


    /**
     * Set the user roles
     *
     * @param array $roles The roles
     * @return $this
     */
    public function setRoles(array $roles)
    {
        $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * Returns the user roles
     *
     * @return array $roles The roles
     */
    public function getRoles()
    {
        $roles = $this->roles;

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    /**
     * Set the value of phone
     *
     * @param string $phone
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get the value of phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set the value of email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of address.
     *
     * @param string $address
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get the value of address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the value of addressExtra.
     *
     * @param string $addressExtra
     * @return $this
     */
    public function setAddressExtra($addressExtra)
    {
        $this->addressExtra = $addressExtra;

        return $this;
    }

    /**
     * Get the value of addressExtra.
     *
     * @return string
     */
    public function getAddressExtra()
    {
        return $this->addressExtra;
    }

    /**
     * Set the value of zipcode.
     *
     * @param string $zipcode
     * @return $this
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get the value of zipcode.
     *
     * @return string
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set the value of city.
     *
     * @param string $city
     * @return $this
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get the value of city.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set the value of website.
     *
     * @param string $website
     * @return $this
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get the value of website.
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set the value of presentation.
     *
     * @param string $presentation
     * @return $this
     */
    public function setPresentation($presentation)
    {
        $this->presentation = $presentation;

        return $this;
    }

    /**
     * Get the value of presentation.
     *
     * @return string
     */
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * {@inheritDoc}
     */
    public function isEnabled()
    {
        return !$this->isLocked();
    }

    /**
     * Set the value of avatar.
     *
     * @param UserImageInterface $avatar
     * @return $this
     */
    public function setAvatar(UserImageInterface $avatar)
    {
        $avatar->setUser($this);
        $avatar->setMaster(true);
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get the value of avatar.
     *
     * @return UserImageInterface
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Get the avatar ID.
     *
     * @return int|null
     */
    public function getAvatarId()
    {
        return $this->avatar ?
            $this->avatar->getId() : null;
    }

    /**
     * Get SearchAnalytics entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLoginAnalytics()
    {
        return $this->loginAnalytics;
    }

    /**
     * Displays a _references param for JMS
     * with related entities data
     *
     * @param UserInterface|null $user
     * @param array     $groups
     * @return array
     */
    public function getReferences(UserInterface $user = null, $groups = array())
    {
        if (empty($this->_references)) {
            $this->_references = array(
                'avatar' => $this->getAvatar()
                //'locale' => $this->getLocale()

            );
        }
        return $this->_references;
    }

    public function __toString()
    {
        return (string) $this->getUsername();
    }
}
