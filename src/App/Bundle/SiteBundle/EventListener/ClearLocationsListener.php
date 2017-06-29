<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 29/06/2017
 * Time: 17:25
 */

namespace App\Bundle\SiteBundle\EventListener;


use eZ\Publish\API\Repository\LocationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ClearLocationsListener implements EventSubscriberInterface
{

    private $locationService;
    private $blogLocationId;
    private $formuleLocationId;
    /**
     * ClearLocationsListener constructor.
     * @param LocationService $locationService
     */
    public function __construct(LocationService $locationService,  $blogLocationId, $formuleLocationId)
    {
        $this->locationService = $locationService;
        $this->blogLocationId = $blogLocationId;
        $this->formuleLocationId = $formuleLocationId;
    }

    public static function getSubscribedEvents()
    {
        return [MVCEvents::CACHE_CLEAR_CONTENT => ['onContentCacheClear', 100]];
    }

    public function onContentCacheClear( ContentCacheClearEvent $event )
    {
        $event->addLocationToClear( $this->locationService->loadLocation( $this->blogLocationId ) );
        $event->addLocationToClear( $this->locationService->loadLocation( $this->formuleLocationId ) );
    }


}
