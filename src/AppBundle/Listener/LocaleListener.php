<?php
namespace AppBundle\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleListener implements EventSubscriberInterface
{
    /**
     * @var string[]
     */
    private $supportedLocales;

    /**
     * LocaleListener constructor.
     * @param array $supportedLocales
     */
    public function __construct(array $supportedLocales)
    {
        $this->supportedLocales = $supportedLocales;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $locale = $request->getPreferredLanguage($this->supportedLocales);
        $request->setLocale($locale);
    }
}