<?php

namespace App\Bundle\SiteBundle\Helper;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Query;
use Symfony\Component\DependencyInjection\Container;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location;

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

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentEntityManager;

    /** @var CriteriaHelper */
    protected $criteriaHelper;

    /** @var \eZ\Publish\API\Repository\SearchService */
    protected $searchService;

    /** @var \Monolog\Logger */
    protected $logger;

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
        $this->criteriaHelper = $this->container->get('app.criteria_helper');
        $this->searchService = $this->repository->getSearchService();
        $this->logger = $this->container->get('logger');
    }

    /**
     * Get latest news (default number = 3)
     * @return array
     */
    public function getLatestNews()
    {
        $newsContentTypeIdentifier = $this->container->getParameter('app.new.content_type.identifier');
        $listingNewsLimit = $this->container->getParameter('app.listing_news.homepage.limit');
        $listingNewsLocationId = $this->container->getParameter('app.listing_news.locationid');

        // Initialize latestNews
        $latestNews = [];

        // Try loading all article under loaded location (listing news)
        try {
            // Generate criteria to get all article under authors listing news class
            $criteriaLatestNews = $this->criteriaHelper->generateContentCriterionByParentLocationIdAndContentIdentifiersAndFieldsData($listingNewsLocationId, [$newsContentTypeIdentifier]);

            // Building Query
            $queryLatestNews = new Query();
            $queryLatestNews->filter = $criteriaLatestNews;
            $queryLatestNews->limit = $listingNewsLimit;
            $queryLatestNews->sortClauses = array(
                //new Location\Priority(Query::SORT_ASC),
                new SortClause\DatePublished(Query::SORT_DESC)
            );

            // Getting results
            $searchResultLatestNews = $this->repository->sudo(
                function() use ($queryLatestNews) {
                    return $this->searchService->findContent($queryLatestNews);
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
        if (isset($searchResultLatestNews->searchHits)) {
            foreach ($searchResultLatestNews->searchHits as $hit) {
                array_push($latestNews, $hit->valueObject);
            }
        }

        return $latestNews;
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

}
