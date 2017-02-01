<?php

namespace App\Bundle\SiteBundle\Helper;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Query;
use Symfony\Component\DependencyInjection\Container;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;

/**
 * CoreHelper Class
 *
 * This class is used to persist data
 * Class to eZ Publish 5 using only the new eZ Publish API and not legacy.
 *
 * @author simoninl
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class CoreHelper
{
    /** @var \eZ\Publish\API\Repository\Repository */
    protected $repository;

    /** @var \Symfony\Component\DependencyInjection\Container */
    protected $container;

    /** @var \eZ\Publish\API\Repository\LocationService */
    protected $locationService;

    /** @var \eZ\Publish\API\Repository\ContentService */
    protected $contentService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentEntityManager;

    /** @var CriteriaHelper */
    protected $criteriaHelper;

    /** @var \eZ\Publish\API\Repository\SearchService */
    protected $searchService;

    /** @var \Monolog\Logger */
    protected $logger;

    /** @var int */
    protected $rootLocationId;

    /** @var $configResolver \eZ\Publish\Core\MVC\ConfigResolverInterface * */
    protected $configResolver;

    /**
     * Constructor
     * @param Repository $repository
     * @param Container  $container
     * @param SiteAccess $siteAccess
     */
    public function __construct(Repository $repository, $container)
    {
        $this->repository = $repository;
        $this->container = $container;
        $this->locationService = $this->repository->getLocationService();
        $this->contentService = $this->repository->getContentService();
        $this->configResolver = $this->container->get('ezpublish.config.resolver');
        $this->rootLocationId = $this->configResolver->getParameter('content.tree_root.location_id');
        $this->criteriaHelper = $this->container->get('app.criteria_helper');
        $this->searchService = $this->repository->getSearchService();
        $this->logger = $this->container->get('logger');
    }

    /**
     * Get content website
     * @return content
     */
    public function getContentHomepage()
    {
        // Loading root location (homepage)
        $homeLocation = $this->locationService->loadLocation($this->rootLocationId);
        // Setting API services
        $contentService = $this->repository->getContentService();

        // Loading homepage content to get website location

        return $contentService->loadContent($homeLocation->contentInfo->id);
    }


    /**
     * Get content Object by Id
     * @param $contentId
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function getContentById($contentId)
    {
        // Setting API services
        $contentService = $this->repository->getContentService();

        return $contentService->loadContent($contentId);
    }

    /**
     * Get article
     * @param string $category
     * @param int    $limit
     * @return array
     */
    public function getLatestArticles($category, $limit = 1, $offset = 0)
    {
        $articleContentTypeIdentifier = $this->container->getParameter('app.article.content_type.identifier');
        $blogLocationId = $this->container->getParameter('app.blog.locationid');

        $fieldsData = ['attribute' => 'category', 'operator' => Operator::EQ, 'value' => $category];

        // Initialize latestNews
        $latestArticles = [];

        // Try loading all article under loaded location (listing news)
        try {
            // Generate criteria to get all article under authors listing news class
            $criteriaLatestArticles = $this->criteriaHelper->generateContentCriterionByParentLocationIdAndContentIdentifiersAndFieldsData($blogLocationId, [$articleContentTypeIdentifier], [$fieldsData]);

            // Building Query
            $queryLatestArticles = new Query();
            $queryLatestArticles->filter = $criteriaLatestArticles;
            $queryLatestArticles->limit = $limit;
            $queryLatestArticles->offset = $offset;
            $queryLatestArticles->sortClauses = array(
                //new Location\Priority(Query::SORT_ASC),
                new SortClause\DatePublished(Query::SORT_DESC)
            );

            // Getting results
            $searchResultLatestArticles = $this->repository->sudo(
                function() use ($queryLatestArticles) {
                    return $this->searchService->findContent($queryLatestArticles);
                }
            );



        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $this->logger->critical($e->getCode());
            $this->logger->critical($e->getFile());
            $this->logger->critical($e->getLine());
            exit("error");
        }
        //var_dump($searchResultLatestNews);die;
        // Building latest News tab
        if (isset($searchResultLatestArticles->searchHits)) {
            foreach ($searchResultLatestArticles->searchHits as $hit) {
                array_push($latestArticles, $hit->valueObject);
            }
        }
        if ($limit == 1 && count($latestArticles) > 0) {
            $latestArticles = $latestArticles[0];
        }


        return $latestArticles;
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
        $searchResult = $this->repository->sudo(
            function() use ($query) {
                return $this->searchService->findContent($query);
            }
        );
        $childrensObject = array();
        if (isset($searchResult->searchHits)) {
            foreach ($searchResult->searchHits as $hit) {
                array_push($childrensObject, $hit->valueObject);
            }
        }

        return $childrensObject;
    }

    public function transformFieldToSelection($value)
    {
        $formatSelectionValue = array(
            $value
        );
        return new \eZ\Publish\Core\FieldType\Selection\Value($formatSelectionValue);
    }

    public function transformEzImage($file)
    {
        $document = null;
        if (!empty($file) || !is_null($file)) {
            $document = new \eZ\Publish\Core\FieldType\Image\Value(
                array(
                    'inputUri' => $file->getRealPath(),
                    'fileSize' => $file->getClientSize(),
                    'fileName' => $file->getClientOriginalName(),
                    'alternativeText' => $file->getClientOriginalName()
                )
            );
        }

        return $document;
    }

    /**
     * Returns options by a selection
     *
     * @param string $contentTypeIdentifier
     * @param string $fieldIdentifier
     *
     * @return boolean|string
     */
    public function getOptionsBySelectionField($contentTypeIdentifier, $fieldIdentifier)
    {
        $contentTypeService = $this->repository->getContentTypeService();
        $contentType = $contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);

        $fieldDefinition = $contentType->getFieldDefinition($fieldIdentifier);
        $options = $fieldDefinition->fieldSettings['options'];

        return $options;
    }

    /**
     * Returns value from selection by key
     *
     * @param string $contentTypeIdentifier
     * @param string $fieldIdentifier
     * @param ing    $key
     *
     * @return boolean|string
     */
    public function getValueFromEzSelectionKey($contentTypeIdentifier, $fieldIdentifier, $key)
    {
        $contentTypeService = $this->repository->getContentTypeService();
        $contentType = $contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);

        $fieldDefinition = $contentType->getFieldDefinition($fieldIdentifier);
        $options = $fieldDefinition->fieldSettings['options'];

        $value = false;

        if (isset($options[$key])) {
            $value = $options[$key];
        }

        return $value;
    }

}
