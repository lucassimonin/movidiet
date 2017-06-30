<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 29/06/2017
 * Time: 17:25
 */

namespace App\Bundle\SiteBundle\EventListener;


use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\Core\MVC\Symfony\Cache\Http\LocalPurgeClient;
use eZ\Publish\Core\MVC\Symfony\Event\SignalEvent;
use eZ\Publish\Core\SignalSlot\Signal\ContentService\PublishVersionSignal;
use EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;

class ClearLocationsListener implements EventSubscriberInterface
{
    /** @var LocationService  */
    private $locationService;

    /** @var LocalPurgeClient  */
    private $purgeClient;
    /**
     * ClearLocationsListener constructor.
     * @param LocationService $locationService
     * @param LocalPurgeClient $purgeClient
     */
    public function __construct(LocationService $locationService, LocalPurgeClient $purgeClient)
    {
        $this->locationService = $locationService;
        $this->purgeClient = $purgeClient;
    }

    public static function getSubscribedEvents()
    {
        return [
            MVCEvents::API_SIGNAL => ['onContentCacheClear', 5]
            ];
    }

    public function onContentCacheClear( SignalEvent $event )
    {
        $signal = $event->getSignal();
        if(!$signal instanceof PublishVersionSignal) {
            return;
        }
        $this->purgeClient->purgeAll();
    }


}
