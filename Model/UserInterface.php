<?php

namespace PhpInk\Nami\CoreBundle\Model;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * User interface
 */
interface UserInterface extends AdvancedUserInterface
{
    /**
     * Set the value of username.
     *
     * @return string
     */
    public function getUsername();

    /**
     * Set the value of username.
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username);


    /**
     * Set the value of salt.
     *
     * @return string
     */
    public function getSalt();

    /**
     * Set the value of salt.
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt);

    /**
     * Gets the encrypted password.
     *
     * @return string
     */
    public function getPassword();

    /**
     * Set the encrypted password.
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password);

    /**
     * Set the plain password.
     *
     * @return string
     */
    public function getPlainPassword();

    /**
     * Set the plain password.
     *
     * @param string $password
     * @return User
     */
    public function setPlainPassword($password);

    /**
     * Sets the user ip
     *
     * @param string $ip
     * @return User
     */
    public function setIp($ip);

    /**
     * Gets the user ip
     *
     * @return string
     */
    public function getIp();

    /**
     * Removes sensitive data from the user.
     */
    public function eraseCredentials();

    /**
     * Gets the last login time.
     *
     * @return \DateTime
     */
    public function getLastLogin();

    /**
     * Sets the last login time.
     *
     * @return \DateTime|null $time
     * @return User
     */
    public function setLastLogin(\DateTime $time = null);


    /**
     * Gets the value of confirmationToken.
     *
     * @return string
     */
    public function getConfirmationToken();

    /**
     * Sets the value of confirmationToken.
     *
     * @param string
     * @return User
     */
    public function setConfirmationToken($confirmationToken);

    /**
     * {@inheritDoc}
     */
    public function isAccountNonExpired();

    /**
     * {@inheritDoc}
     */
    public function isAccountNonLocked();


    /**
     * {@inheritDoc}
     */
    public function isCredentialsNonExpired();

    /**
     * @param boolean $active
     *
     * @return User
     */
    public function setActive($active);

    public function isActive();


    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName);

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName();

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName);

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName();

    /**
     * Gets the value of locked.
     *
     * @return bool
     */
    public function isLocked();


    /**
     * Sets the value of locked.
     *
     * @param bool
     * @return User
     */
    public function setLocked($boolean);

    /**
     * Set the value for passwordRequestedAt.
     *
     * @param \DateTime
     * @return User
     */
    public function setPasswordRequestedAt(\DateTime $date = null);

    /**
     * Gets the timestamp that the user requested a password reset.
     *
     * @return null|\DateTime
     */
    public function getPasswordRequestedAt();

    /**
     * {@inheritDoc}
     */
    public function isPasswordRequestNonExpired($ttl);

    /**
     * Set male
     *
     * @param boolean $male
     * @return User
     */
    public function setMale($male);

    /**
     * Get male
     *
     * @return boolean
     */
    public function isMale();

    /**
     * Has role(s) methods
     *
     * @return bool
     */
    public function isAdmin();

    /**
     * Sets the Manager ROLE.
     *
     * @param $boolean
     * @return User
     */
    public function setAdmin($boolean);

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AuthorizationCheckerInterface
     *
     * @param string $role
     *
     * @return boolean
     */
    public function hasRole($role);

    /**
     * Remove a user role
     *
     * @param string $role The role
     * @return User
     */
    public function removeRole($role);

    /**
     * Add a user role
     *
     * @param string $role The role
     * @return User
     */
    public function addRole($role);


    /**
     * Set the user roles
     *
     * @param array $roles The roles
     * @return User
     */
    public function setRoles(array $roles);

    /**
     * Returns the user roles
     *
     * @return array $roles The roles
     */
    public function getRoles();

    /**
     * Set the value of phone
     *
     * @param string $phone
     * @return Phone
     */
    public function setPhone($phone);

    /**
     * Get the value of phone
     *
     * @return string
     */
    public function getPhone();

    /**
     * Set the value of email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email);

    /**
     * Get the value of email
     *
     * @return string
     */
    public function getEmail();

    /**
     * Set the value of address.
     *
     * @param string $address
     * @return Company
     */
    public function setAddress($address);

    /**
     * Get the value of address.
     *
     * @return string
     */
    public function getAddress();

    /**
     * Set the value of addressExtra.
     *
     * @param string $addressExtra
     * @return Company
     */
    public function setAddressExtra($addressExtra);

    /**
     * Get the value of addressExtra.
     *
     * @return string
     */
    public function getAddressExtra();

    /**
     * Set the value of zipcode.
     *
     * @param string $zipcode
     * @return Company
     */
    public function setZipcode($zipcode);

    /**
     * Get the value of zipcode.
     *
     * @return string
     */
    public function getZipcode();

    /**
     * Set the value of city.
     *
     * @param string $city
     * @return Company
     */
    public function setCity($city);

    /**
     * Get the value of city.
     *
     * @return string
     */
    public function getCity();

    /**
     * Set the value of website.
     *
     * @param string $website
     * @return Company
     */
    public function setWebsite($website);

    /**
     * Get the value of website.
     *
     * @return string
     */
    public function getWebsite();

    /**
     * Set the value of presentation.
     *
     * @param string $presentation
     * @return Company
     */
    public function setPresentation($presentation);

    /**
     * Get the value of presentation.
     *
     * @return string
     */
    public function getPresentation();

    /**
     * {@inheritDoc}
     */
    public function isEnabled();

    /**
     * Set the value of avatar.
     *
     * @param ImageInterface $avatar
     * @return User
     */
    public function setAvatar(ImageInterface $avatar);

    /**
     * Get the value of avatar.
     *
     * @return Image
     */
    public function getAvatar();

    /**
     * Get the avatar ID.
     *
     * @return int|null
     */
    public function getAvatarId();

    /**
     * Get SearchAnalytics entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLoginAnalytics();

    /**
     * Displays a _references param for JMS
     * with related entities data
     *
     * @param UserInterface|null $user
     * @param array     $groups
     * @return array
     */
    public function getReferences(UserInterface $user = null, $groups = array());
}
