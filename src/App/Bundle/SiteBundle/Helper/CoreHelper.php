<?php

namespace Despas\Bundle\SiteBundle\Helper;

use eZ\Publish\API\Repository\Repository;
use eZ\Bundle\EzPublishLegacyBundle\LegacyMapper\SiteAccess;
use eZ\Publish\API\Repository\Values\Content\Query;
use Symfony\Component\DependencyInjection\Container;

/**
 * UserEntityManager Class
 *
 * This class is used to persist data
 * Class to eZ Publish 5 using only the new eZ Publish API and not legacy.
 *
 * @author kapetanosm
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class CoreHelper
{
    /** @var \eZ\Publish\API\Repository\Repository */
    protected $repository;

    /** @var \Symfony\Component\DependencyInjection\Container */
    protected $container;

    /** @var \Closure */
    protected $legacyKernel;

    /** @var $configResolver \eZ\Publish\Core\MVC\ConfigResolverInterface * */
    protected $configResolver;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentEntityManager;

    /** @var \eZ\Bundle\EzPublishLegacyBundle\LegacyMapper\SiteAccess */
    protected $siteAccess;

    /** @var \eZ\Publish\API\Repository\LocationService */
    protected $locationService;

    /** @var \eZ\Publish\API\Repository\ContentService */
    protected $contentService;

    /** @var \Mrt\SiteBundle\Helper\CriteriaHelper */
    protected $criteriaHelper;

    /** @var \eZ\Publish\API\Repository\SearchService */
    protected $searchService;

    /** @var \Monolog\Logger */
    protected $logger;

    /** @var int */
    protected $rootLocationId;

    /** @var string @ */
    protected $translationSiteAccess;

    /** @var \eZ\Publish\Core\MVC\Symfony\Routing\Generator\RouteReferenceGeneratorInterface $routeRefGenerator */
    protected $routeRefGenerator;

    /**
     * Constructor
     * @param Repository $repository
     * @param Container  $container
     * @param \Closure   $legacyKernel
     * @param SiteAccess $siteAccess
     */
    public function __construct(Repository $repository, $container, \Closure $legacyKernel, $siteAccess)
    {
        $this->repository = $repository;
        $this->container = $container;
        $this->legacyKernel = $legacyKernel;
        $this->siteAccess = $siteAccess;
        $this->configResolver = $this->container->get('ezpublish.config.resolver');
        $this->contentEntityManager = $this->container->get('mrt_site.content_entity_manager');
        $this->rootLocationId = $this->configResolver->getParameter('content.tree_root.location_id');
        $this->locationService = $this->repository->getLocationService();
        $this->contentService = $this->repository->getContentService();
        $this->criteriaHelper = $this->container->get('mrt_site.criteria_helper');
        $this->searchService = $this->repository->getSearchService();
        $this->logger = $this->container->get('logger');
        $this->routeRefGenerator = $this->container->get( 'ezpublish.route_reference.generator' );
        $this->translationSiteAccess = $this->container->hasParameter($this->getSiteAccess() . '.parent') ? $this->container->getParameter($this->getSiteAccess() . '.parent') : '';
    }

    /**
     * Get current siteaccess
     * @return string
     */
    public function getSiteAccess()
    {
        return $this->siteAccess->name;
    }

    /**
     * get root location Id
     * @return string
     */
    public function getRootLocationId()
    {
        return $this->rootLocationId;
    }

    /**
     * Get breadcrumb
     *
     * @param int $locationId
     */
    public function getBreadcrumb($locationId)
    {
        $breadcrumbArray = array();
        // Get home
        if($locationId != $this->rootLocationId) {
            $rootLocation = $this->locationService->loadLocation($this->rootLocationId);
            $routeRef = $this->routeRefGenerator->generate($rootLocation);
            $link = $this->container->get('router')->generate($routeRef, array(), false);
            array_push($breadcrumbArray, array('name' => $this->container->get('translator')->trans('site_authors.' . $this->getTranslationSiteAccess() . '.breadcrumb.home'), 'link' => $link));
        }
        // Get other elements
        $i = 0;
        $elementArray = array();
        while($locationId != $this->rootLocationId) {
            $objectLocation = $this->locationService->loadLocation($locationId);
            $locationId = $objectLocation->parentLocationId;
            $link = '';
            // Not link on the last element
            if($i > 0) {
                $routeRef = $this->routeRefGenerator->generate($objectLocation);
                $link = $this->container->get('router')->generate($routeRef, array(), false);
            }
            array_push($elementArray, array('name' => $objectLocation->contentInfo->name, 'link' => $link));
            $i++;
        }
        $breadcrumbArray = array_merge($breadcrumbArray, array_reverse($elementArray));

        return $breadcrumbArray;
    }

    /**
     * Get siteaccess for translation
     */
    public function getTranslationSiteAccess()
    {
        return $this->translationSiteAccess;
    }

    /**
     * Get configuration file
     * @return array
     */
    public function getConfigurationSite()
    {
        $parameters = array();
        // Load homepage
        $rootLocation = $this->locationService->loadLocation($this->rootLocationId);
        $websiteLocation = $this->locationService->loadLocation($rootLocation->parentLocationId);
        $contentTypes = [$this->container->getParameter('site_authors.configuration_tpl.content_type.identifier')];
        $criteria = $this->criteriaHelper->generateContentCriterionByParentLocationIdAndContentIdentifiersAndFieldsData($websiteLocation->id, $contentTypes);
        try {
            $searchResult = $this->searchService->findSingle($criteria);
            $parameters['color_one'] = $searchResult->getFieldValue('color_one')->text;
            $parameters['color_two'] = $searchResult->getFieldValue('color_two')->text;
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $this->logger->critical($e->getCode());
            $this->logger->critical($e->getFile());
            $this->logger->critical($e->getLine());
        }

        return $parameters;
    }

    /**
     * Return an array with homepage configuration (display attributes)
     * @return array
     */
    public function getDisplayConfigurationHomepage()
    {
        $parameters = array();

        try {
            // Load homepage
            $rootLocation = $this->locationService->loadLocation($this->rootLocationId);
            $homepageContent = $this->contentService->loadContent($rootLocation->contentId);
            // Setting parameters array
            $parameters['display_therapeutic_area'] = $homepageContent->getFieldValue('display_therapeutic_area')->bool;
            $parameters['display_introduction'] = $homepageContent->getFieldValue('display_introduction')->bool;
            $parameters['display_news'] = $homepageContent->getFieldValue('display_news')->bool;
            $parameters['display_glossary'] = $homepageContent->getFieldValue('display_glossary')->bool;
            $parameters['display_faq'] = $homepageContent->getFieldValue('display_faq')->bool;
            $parameters['display_how_to'] = $homepageContent->getFieldValue('display_how_to')->bool;
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $this->logger->critical($e->getCode());
            $this->logger->critical($e->getFile());
            $this->logger->critical($e->getLine());
        }
        return $parameters;
    }

    /**
     * Get repository
     * @return \eZ\Publish\API\Repository\Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Get Container
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Get Search services
     * @return \eZ\Publish\API\Repository\SearchService
     */
    public function getSearchService()
    {
        return $this->searchService;
    }

    /**
     * Get criteria helper
     * @return \Mrt\SiteBundle\Helper\CriteriaHelper
     */
    public function getCriteriaHelper()
    {
        return $this->criteriaHelper;
    }

    /**
     * Get location service
     * @return \eZ\Publish\API\Repository\LocationService
     */
    public function getLocationService()
    {
        return $this->locationService;
    }

    /**
     * Get content entity manager
     * @return \eZ\Publish\API\Repository\ContentTypeService
     */
    public function getContentEntityManager()
    {
        return $this->contentEntityManager;
    }

    /**
     * Skeleton of object in BO
     * @param string $siteName
     * @return array
     */
    public function generateTreeSiteAuthors($siteName)
    {
        $questionnaireHelper = $this->container->get('mrt_user.questionnaire_helper');

        return array(
            // Site container
            $this->container->getParameter('site_authors.site_container.content_type.identifier') => array('locationId' => 0, 'parentLocationId' => null),
            // Website
            $this->container->getParameter('site_authors.website.content_type.identifier') => array(
                'parentLocationId' => $this->container->getParameter('site_authors.site_container.content_type.identifier'),
                'locationId' => 0,
                'attributes' => array('name' => $siteName)
            ),
            // Configuration TPL
            $this->container->getParameter('site_authors.configuration_tpl.content_type.identifier') => array(
                'parentLocationId' => $this->container->getParameter('site_authors.website.content_type.identifier'),
                'locationId' => 0,
                'attributes' => array('name' => $this->container->getParameter('site_authors.configuration_tpl.init_name'))
            ),
            // Home page
            $this->container->getParameter('site_authors.homepage.content_type.identifier') => array(
                'parentLocationId' => $this->container->getParameter('site_authors.website.content_type.identifier'),
                'locationId' => 0,
                'attributes' => array('name' => $this->container->getParameter('site_authors.homepage.init_name'))
            ),
            // Condition of use
            $this->container->getParameter('site_authors.condition_of_use.content_type.identifier') => array(
                'parentLocationId' => $this->container->getParameter('site_authors.homepage.content_type.identifier'),
                'locationId' => 0,
                'attributes' => array('name' => $this->container->getParameter('site_authors.condition_of_use.init_name'))
            ),
            // Folder article
            $this->container->getParameter('site_authors.listing_article.content_type.identifier') => array(
                'parentLocationId' => $this->container->getParameter('site_authors.homepage.content_type.identifier'),
                'locationId' => 0,
                'attributes' => array('name' => $this->container->getParameter('site_authors.listing_article.init_name'))
            ),
            // Article
            $this->container->getParameter('site_authors.article.content_type.identifier') => array(
                'parentLocationId' => $this->container->getParameter('site_authors.listing_article.content_type.identifier'),
                'locationId' => 0,
                'attributes' => array('name' => $this->container->getParameter('site_authors.article.init_name'))
            ),
            // FAQ
            $this->container->getParameter('site_authors.faq.content_type.identifier') => array(
                'parentLocationId' => $this->container->getParameter('site_authors.homepage.content_type.identifier'),
                'locationId' => 0,
                'attributes' => array('name' => $this->container->getParameter('site_authors.faq.init_name'))
            ),
            // Question FAQ
            $this->container->getParameter('site_authors.question_faq.content_type.identifier') => array(
                'parentLocationId' => $this->container->getParameter('site_authors.faq.content_type.identifier'),
                'locationId' => 0,
                'attributes' => array('name' => $this->container->getParameter('site_authors.question_faq.init_name'))
            ),
            // Glossary
            $this->container->getParameter('site_authors.glossary.content_type.identifier') => array(
                'parentLocationId' => $this->container->getParameter('site_authors.homepage.content_type.identifier'),
                'locationId' => 0,
                'attributes' => array('name' => $this->container->getParameter('site_authors.glossary.init_name'))
            ),
            // How to
            $this->container->getParameter('site_authors.how_to.content_type.identifier') => array(
                'parentLocationId' => $this->container->getParameter('site_authors.homepage.content_type.identifier'),
                'locationId' => 0,
                'attributes' => array('name' => $this->container->getParameter('site_authors.how_to.init_name'))
            ),
            // Listing COA
            $this->container->getParameter('site_authors.listing_coa.content_type.identifier') => array(
                'parentLocationId' => $this->container->getParameter('site_authors.homepage.content_type.identifier'),
                'locationId' => 0,
                'attributes' => array(
                    'name' => $this->container->getParameter('site_authors.listing_coa.init_name')
                )
            ),
            // COA
            $this->container->getParameter('site_authors.coa.content_type.identifier') => array(
                'parentLocationId' => $this->container->getParameter('site_authors.listing_coa.content_type.identifier'),
                'locationId' => 0,
                'attributes' => array('name' => $this->container->getParameter('site_authors.coa.init_name'))
            ),
            //Folder therapeutic area
            $this->container->getParameter('site_authors.therapeutic_area_container.content_type.identifier') => array(
                'parentLocationId' => $this->container->getParameter('site_authors.homepage.content_type.identifier'),
                'locationId' => 0,
                'attributes' => array('name' => $this->container->getParameter('site_authors.therapeutic_area_container.init_name'))
            ),
            // Placeholder
            $this->container->getParameter('site_authors.placeholder.content_type.identifier') => array(
                'parentLocationId' => $this->container->getParameter('site_authors.therapeutic_area_container.content_type.identifier'),
                'locationId' => 0,
                'attributes' => array('name' => $this->container->getParameter('site_authors.placeholder.init_name'))
            ),
            // Therapeutic area object
            $this->container->getParameter('site_authors.therapeutic_area.content_type.identifier') => array(
                'parentLocationId' => $this->container->getParameter('site_authors.therapeutic_area_container.content_type.identifier'),
                'locationId' => 0,
                'attributes' => array('name' => $this->container->getParameter('site_authors.therapeutic_area.init_name'))
            ),
            // Sickness
            $this->container->getParameter('site_authors.sickness.content_type.identifier') => array(
                'parentLocationId' => $this->container->getParameter('site_authors.therapeutic_area.content_type.identifier'),
                'locationId' => 0,
                'attributes' => array('name' => $this->container->getParameter('site_authors.sickness.init_name'))
            ),
        );
    }

    /**
     * Generate object in BO
     *
     * @param string  $siteName
     * @param OutputInterface $output
     * @return number
     */
    public function generateSkeletonAuthors($siteName, $output = null)
    {
        // Root node ID
        $locationId = 2;
        $contentTypes = [$this->container->getParameter('site_authors.site_container.content_type.identifier')];
        $objectsTree = $this->generateTreeSiteAuthors($siteName);

        $criteria = $this->criteriaHelper->generateContentCriterionByParentLocationIdAndContentIdentifiersAndFieldsData($locationId, $contentTypes);
        $siteAuthorsContainerLocationId = null;
        $homepageLocationId = null;
        try {
            $searchResult = $this->repository->sudo(
                    function() use($criteria) {
                return $this->searchService->findSingle($criteria);
            }
            );

            $siteAuthorsContainerLocationId = $searchResult->versionInfo->contentInfo->mainLocationId;
        } catch (\Exception $e) {
            // In some case collaborative space object doesn't exists under a customer account
            // So we have to create it
            if ($e->getCode() === 404) {
                if ($output != null) {
                    $output->writeln($this->getHelperSet()->get('formatter')->formatBlock($e->getMessage(), 'error'));
                }

                $siteAuthorsContainer = $this->contentEntityManager->createContent($this->container->getParameter('site_authors.site_container.content_type.identifier'), ['name' => 'Sites authors'], $locationId);
                $siteAuthorsContainerLocationId = $siteAuthorsContainer->versionInfo->contentInfo->mainLocationId;
                if ($output != null) {
                    $output->writeln('Sites authors container created : <info>OK</info>');
                }
            }
        }

        // Create all object in BO
        foreach ($objectsTree as $id => $objectTree) {
            if ($objectTree['parentLocationId'] == null) {
                $objectsTree[$id]['locationId'] = intval($siteAuthorsContainerLocationId);
            } else {
                $object = $this->contentEntityManager->createContent($id == 'condition_of_use' ? 'authors_article' : $id, $objectTree['attributes'], $objectsTree[$objectTree['parentLocationId']]['locationId']);
                $objectsTree[$id]['locationId'] = $object->versionInfo->contentInfo->mainLocationId;
                if ($output != null) {
                    $output->writeln($id . ' Object created : <info>OK</info>');
                }
            }
            if ($id == 'authors_homepage') {
                $homepageLocationId = intval($objectsTree[$id]['locationId']);
            }
        }

        return $homepageLocationId;
    }

    /**
     * Returns THE location Id of input content identifier, usefull for containers only
     * The root location is the homepage Location
     * @param array $contentTypeIdentifier
     * @return string
     * @throws \Exception if operation fail (return 404)
     */
    public function getLocationIdByContentIdentifier($contentTypeIdentifier)
    {
        // Getting home page location
        $homePageLocation = $this->locationService->loadLocation($this->rootLocationId);
        try {
            $criteria = $this->criteriaHelper->generateContentCriterionByParentLocationIdAndContentIdentifiersAndFieldsData($homePageLocation->id, $contentTypeIdentifier);
            $searchResult = $this->searchService->findSingle($criteria);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $this->logger->critical($e->getMessage());
            $this->logger->critical($e->getCode());
            $this->logger->critical($e->getFile());
            $this->logger->critical($e->getLine());
            exit();
            // TODO redirect
        }

        return $searchResult->versionInfo->contentInfo->mainLocationId;
    }

    /**
     *
     * @param array      $contentType
     * @param string|int $locationId
     * @return array
     */
    public function getChildrenObject($contentType, $locationId)
    {
        $criteria = $this->criteriaHelper->generateContentCriterionByParentLocationIdAndContentIdentifiersAndFieldsData($locationId, $contentType);
        $query = new Query();
        $query->filter = $criteria;
        $searchResult = $this->searchService->findContent($query);
        $childrensObject = array();
        if (isset($searchResult->searchHits)) {
            foreach ($searchResult->searchHits as $hit) {
                array_push($childrensObject, $hit->valueObject);
            }
        }

        return $childrensObject;
    }

    /**
     * Get content website
     * @return content
     */
    public function getContentWebsite()
    {
        // Loading root location (homepage)
        $rootLocation = $this->locationService->loadLocation($this->rootLocationId);

        // Setting API services
        $contentService = $this->repository->getContentService();

        // Loading homepage content to get website location
        $websiteLocation = $this->locationService->loadLocation($rootLocation->parentLocationId);
        $websiteContent = $contentService->loadContent($websiteLocation->contentInfo->id);

        return $websiteContent;
    }

    /**
     * Get disable website
     * @return bool
     */
    public function getEnabledSite()
    {
        if(($this->getSiteAccess() != 'mrt_ecommerce_en') and ($this->getSiteAccess() != 'mrt_ecommerce_admin'))
        {
            $websiteContent = $this->getContentWebsite();
            return $websiteContent->getFieldValue('enabled')->bool;
        }
        else
        {
            return true;
        }
    }

}
