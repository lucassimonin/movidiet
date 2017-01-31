<?php
/**
 * Created by PhpStorm.
 * User: Luk
 * Date: 24/01/2017
 * Time: 20:32
 */

namespace App\Bundle\SiteBundle\Helper;

use App\Bundle\SiteBundle\Entity\User;
use eZ\Publish\API\Repository\Repository;
use Symfony\Component\DependencyInjection\Container;

class UserHelper
{

    const DEFAULT_LOCATION_MEMBERS_ID = 11,
        DEFAULT_LANGUAGE = 'fre-FR',
        DEFAULT_ADMIN_ID = 14;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /**
     * @var Container
     */
    private $container;

    /**
     * Constructor
     * @param Repository $repository
     * @param Container  $container
     */
    public function __construct(Repository $repository, $container)
    {
        $this->repository = $repository;
        $this->container = $container;
    }

    /**
     * Saves to eZ new user
     *
     * @param \App\Bundle\SiteBundle\Entity\User $user
     * @param int $locationId
     * @return bool|\eZ\Publish\API\Repository\Values\User\User
     */
    public function save(User $user, $locationId = null)
    {
        $logger = $this->container->get('logger');
        $userService = $this->repository->getUserService();
        $coreHelper = $this->container->get('app.core_helper');

        try {
            $adminId = self::DEFAULT_ADMIN_ID;
            $currentUser = $this->repository->getCurrentUser();

            $admin = $userService->loadUser($adminId);
            $this->repository->setCurrentUser($admin);

            $language = self::DEFAULT_LANGUAGE;

            $userCreateStruct = $userService->newUserCreateStruct(
                $this->generateRandomLogin($user->email),
                $user->email,
                $user->password,
                $language
            );
            $userCreateStruct->enabled = true;

            $userCreateStruct->setField('last_name', $user->lastName);
            $userCreateStruct->setField('first_name', $user->firstName);
            $userCreateStruct->setField('country', $user->country);
            $userCreateStruct->setField('street', $user->street);
            $userCreateStruct->setField('birthday_date', $user->birthday);
            $userCreateStruct->setField('phone', $user->phone);
            $userCreateStruct->setField('postal_code', $user->postalCode);
            $userCreateStruct->setField('city', $user->city);
            $userCreateStruct->setField('height', $user->height);
            $userCreateStruct->setField('weight', $user->weight);
            $userCreateStruct->setField('formule', $coreHelper->transformFieldToSelection($user->formule));
            $userCreateStruct->setField('image',  $coreHelper->transformEzImage($user->image));

            if (is_null($locationId)) {
                $locationId = self::DEFAULT_LOCATION_MEMBERS_ID;
            }

            $userGroup = $userService->loadUserGroup($locationId);
            $userStruct = $userService->createUser($userCreateStruct, array($userGroup));

            $this->repository->setCurrentUser($currentUser);
        } catch (\Exception $e) {
            $logger->critical('An error occured on UserHelper::save, exception: ' . $e->getMessage());
            $this->repository->setCurrentUser($currentUser);

            return false;
        }

        return $userStruct;
    }

    /**
     * Update user by $userId given
     *
     * @param User $user
     * @return boolean
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) heavy business rules
     * @SuppressWarnings(PHPMD.NPathComplexity) heavy business rules
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength) +100
     */
    public function update(User $user)
    {
        $logger = $this->container->get('logger');
        $userService = $this->repository->getUserService();

        try {
            $adminId = self::DEFAULT_ADMIN_ID;

            $currentUser = $this->repository->getCurrentUser();

            $admin = $userService->loadUser($adminId);
            $this->repository->setCurrentUser($admin);

            $ezUser = $userService->loadUser($user->id);

            $contentService = $this->repository->getContentService();
            $userUpdateStruct = $userService->newUserUpdateStruct();
            $contentUpdateStruct = $contentService->newContentUpdateStruct();

            // $userUpdateStruct data
            if (!is_null($user->email)) {
                $userUpdateStruct->email = $user->email;
            }
            if (!is_null($user->password)) {
                $userUpdateStruct->password = $user->password;
            }

            // Custom fields
            if (!is_null($user->street)) {
                $contentUpdateStruct->setField('street', $user->street);
            }
            if (!is_null($user->lastName)) {
                $contentUpdateStruct->setField('last_name', $user->lastName);
            }
            if (!is_null($user->firstName)) {
                $contentUpdateStruct->setField('first_name', $user->firstName);
            }
            if (!is_null($user->country) && !empty($user->country)) {
                $contentUpdateStruct->setField('country', [$user->country]);
            }
            if (!is_null($user->birthday)) {
                $contentUpdateStruct->setField('birthday_date', $user->birthday);
            }
            if (!is_null($user->phone)) {
                $contentUpdateStruct->setField('phone', $user->phone);
            }
            if (!is_null($user->postalCode)) {
                $contentUpdateStruct->setField('postal_code', $user->postalCode);
            }
            if (!is_null($user->city)) {
                $contentUpdateStruct->setField('city', $user->city);
            }
            if (!is_null($user->height)) {
                $contentUpdateStruct->setField('height', $user->height);
            }
            if (!is_null($user->weight)) {
                $contentUpdateStruct->setField('weight', $user->weight);
            }
            if (!is_null($user->formule)) {
                $contentUpdateStruct->setField('formule', $user->formule);
            }

            $userUpdateStruct->contentUpdateStruct = $contentUpdateStruct;
            $userService->updateUser($ezUser, $userUpdateStruct);

            $this->container->get('ezpublish.http_cache.purger')->purge($ezUser->versionInfo->contentInfo->mainLocationId);

            $this->repository->setCurrentUser($currentUser);

            return true;
        } catch (\Exception $e) {
            $logger->critical('An error occured on UserHelper::update, exception: ' . $e->getMessage());
            $this->repository->setCurrentUser($currentUser);

            return false;
        }
    }

    public function loadUserObjectByEzApiUser(User $user, $userId)
    {
        $userService = $this->repository->getUserService();

        try {
            $userStruct = $userService->loadUser($userId);
        } catch (\Exception $e) {
            return false;
        }

        $user->setId($userId);
        $user->setFirstName((string) $userStruct->getFieldValue('first_name'));
        $user->setLastName((string) $userStruct->getFieldValue('last_name'));
        $user->setEmail((string) $userStruct->email);
        $user->setCountry((string) $userStruct->getFieldValue('country'));
        $user->setStreet((string) $userStruct->getFieldValue('street'));
        $user->setPostalCode((string) $userStruct->getFieldValue('postal_code'));
        $user->setPhone((string) $userStruct->getFieldValue('phone'));
        $user->setCity((string) $userStruct->getFieldValue('city'));
        $user->setBirthday((string) $userStruct->getFieldValue('birthday_date'));
    }

    private function generateRandomLogin($email)
    {
        $uniqueString = $email . md5(uniqid(mt_rand(), true));
        if (strlen($uniqueString) > 149) {
            $uniqueString = substr($uniqueString, 0, 149);
        }

        return $uniqueString;
    }

}