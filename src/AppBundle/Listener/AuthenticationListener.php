<?php
namespace AppBundle\Listener;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

class AuthenticationListener implements EventSubscriberInterface
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * AuthenticationListener constructor.
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            AuthenticationEvents::AUTHENTICATION_FAILURE => 'onFailure',
            AuthenticationEvents::AUTHENTICATION_SUCCESS => 'onSuccess',
        );
    }

    /**
     * @param AuthenticationEvent $event
     */
    public function onFailure(AuthenticationEvent $event)
    {
        $this->logger->debug('FAILURE : '.$event->getAuthenticationToken()->getUsername());
    }

    /**
     * @param AuthenticationEvent $event
     */
    public function onSuccess(AuthenticationEvent $event)
    {
        $this->logger->debug('SUCCESS : '.$event->getAuthenticationToken()->getUsername());
    }
}
