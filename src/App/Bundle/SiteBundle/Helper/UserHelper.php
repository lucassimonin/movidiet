<?php
/**
 * Created by PhpStorm.
 * User: Luk
 * Date: 24/01/2017
 * Time: 20:32
 */

namespace App\Bundle\SiteBundle\Helper;

use App\Bundle\SiteBundle\Entity\User;
use eZ\Bundle\EzPublishCoreBundle\Cache\Http\InstantCachePurger;
use eZ\Publish\API\Repository\Repository;
use Symfony\Component\DependencyInjection\Container;
use DateTime;

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
                $user->account,
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
            $userCreateStruct->setField('sex', $coreHelper->transformFieldToSelection($user->sex));
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
    public function update(User $user, $updateUser = false)
    {
        $logger = $this->container->get('logger');
        $userService = $this->repository->getUserService();
        $coreHelper = $this->container->get('app.core_helper');

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
            if($updateUser) {
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
                    $contentUpdateStruct->setField('country', $user->country);
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
                    $contentUpdateStruct->setField('height', (string)$user->height);
                }
                if (!is_null($user->weight)) {
                    $contentUpdateStruct->setField('weight', (string)$user->weight);
                }
                if (!is_null($user->fatMass)) {
                    $contentUpdateStruct->setField('fat_mass', (string)$user->fatMass);
                }

                if(!is_null($user->image)) {
                    $contentUpdateStruct->setField('image',  $coreHelper->transformEzImage($user->image));
                }
                if(!is_null($user->sex)) {
                    $contentUpdateStruct->setField('sex', $coreHelper->transformFieldToSelection($user->sex));
                }
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
        $user->setWeight(floatval((string) $userStruct->getFieldValue('weight')));
        $user->setHeight((string) $userStruct->getFieldValue('height'));
        $user->setFatMass(floatval((string) $userStruct->getFieldValue('fat_mass')));
        $coreHelper = $this->container->get('app.core_helper');
        if(!empty($userStruct->getFieldValue('sex')->selection)) {
            $user->setSex($coreHelper->getValueFromEzSelectionKey('user', 'sex', $userStruct->getFieldValue('sex')->selection[0]));
        }
    }

    private function generateRandomLogin($email)
    {
        $uniqueString = $email . md5(uniqid(mt_rand(), true));
        if (strlen($uniqueString) > 149) {
            $uniqueString = substr($uniqueString, 0, 149);
        }

        return $uniqueString;
    }

    public function getColorFatMass(User $user)
    {
        $age = $this->getAge($user);
        $sex = $user->getSex();
        $fatMass = $user->getFatMass();
        $color = 'circleGrey';
        if ($fatMass != null && $fatMass > 0 &&  $sex != null && $age != null) {
            if ($sex == 'Homme') {
                if ($age > 16 && $age < 30) {
                    if ($fatMass < 9.2 || $fatMass >= 26.2) {
                        $color = 'circleRed';
                    } elseif ($fatMass >= 9.2 && $fatMass < 14.7 || $fatMass > 21.1 && $fatMass < 26.2) {
                        $color = 'circleOrange';
                    } elseif ($fatMass >= 14.7 && $fatMass < 21.1) {
                        $color = 'circleGreen';
                    }
                } elseif ($age >= 30 && $age < 40) {
                    if ($fatMass < 12.2 || $fatMass >= 27.2) {
                        $color = 'circleRed';
                    } elseif ($fatMass >= 12.2 && $fatMass < 16.2 || $fatMass >= 22.5 && $fatMass < 27.2) {
                        $color = 'circleOrange';
                    } elseif ($fatMass >= 16.2 && $fatMass < 22.5) {
                        $color = 'circleGreen';
                    }
                } elseif ($age >= 40 && $age < 50) {
                    if ($fatMass < 12.2 || $fatMass >= 31.2) {
                        $color = 'circleRed';
                    } elseif ($fatMass >= 12.2 && $fatMass < 17.7 || $fatMass >= 25.9 && $fatMass < 31.2) {
                        $color = 'circleOrange';
                    } elseif ($fatMass >= 17.7 && $fatMass < 25.9) {
                        $color = 'circleGreen';
                    }
                } elseif ($age >= 50) {
                    if ($fatMass < 12.5 || $fatMass >= 31.6) {
                        $color = 'circleRed';
                    } elseif ($fatMass >= 12.5 && $fatMass < 18.6 || $fatMass >= 26.5 && $fatMass < 31.6) {
                        $color = 'circleOrange';
                    } elseif ($fatMass >= 18.6 && $fatMass < 26.5) {
                        $color = 'circleGreen';
                    }
                }
            } else {
                if ($age > 16 && $age < 30) {
                    if ($fatMass < 15.4 || $fatMass >= 31.2) {
                        $color = 'circleRed';
                    } elseif ($fatMass >= 15.4 && $fatMass < 19.5 || $fatMass > 26.5 && $fatMass < 31.2) {
                        $color = 'circleOrange';
                    } elseif ($fatMass >= 19.5 && $fatMass < 26.5) {
                        $color = 'circleGreen';
                    }
                } elseif ($age >= 30 && $age < 40) {
                    if ($fatMass < 17 || $fatMass >= 22.5) {
                        $color = 'circleRed';
                    } elseif ($fatMass >= 17 && $fatMass < 20.9 || $fatMass >= 26.9 && $fatMass < 32.5) {
                        $color = 'circleOrange';
                    } elseif ($fatMass >= 20.9 && $fatMass < 26.9) {
                        $color = 'circleGreen';
                    }
                } elseif ($age >= 40 && $age < 50) {
                    if ($fatMass < 12.2 || $fatMass >= 31.2) {
                        $color = 'circleRed';
                    } elseif ($fatMass >= 12.2 && $fatMass < 23.4 || $fatMass >= 29.6 && $fatMass < 31.2) {
                        $color = 'circleOrange';
                    } elseif ($fatMass >= 23.4 && $fatMass < 29.6) {
                        $color = 'circleGreen';
                    }
                } elseif ($age >= 50) {
                    if ($fatMass < 21.4 || $fatMass >= 36.7) {
                        $color = 'circleRed';
                    } elseif ($fatMass >= 21.4 && $fatMass < 24.8 || $fatMass >= 31.9 && $fatMass < 36.7) {
                        $color = 'circleOrange';
                    } elseif ($fatMass >= 24.8 && $fatMass < 31.9) {
                        $color = 'circleGreen';
                    }
                }
            }
        }

        return $color;
    }

    public function getAge(User $user)
    {
        $from = new DateTime($user->getBirthday());
        $to   = new DateTime('today');

        return $from->diff($to)->y;;
    }

}
