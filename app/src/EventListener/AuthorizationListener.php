<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuthorizationListener implements EventSubscriberInterface
{
    /**
     * @param RouterInterface $router
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        private readonly RouterInterface $router,
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    /**
     * @param RequestEvent $event
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $this->setLanguage($event);
        $this->isUserBlocked($event);
    }

    private function isUserBlocked(RequestEvent $event): void
    {
        if (empty($this->tokenStorage->getToken()) || !$this->tokenStorage->getToken()?->getUser() instanceof User) {
            return;
        }

        if (!$this->tokenStorage->getToken()?->getUser()?->getIsActive()) {
            $event->setResponse(new RedirectResponse($this->router->generate('app_logout')));
        }
    }

    /**
     * @param RequestEvent $event
     * @return void
     */
    private function setLanguage(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if ($request->headers->has("Accept-Language")) {
            $locale = $request->headers->get('Accept-Language');
            $request->setLocale($locale);
        }
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 200]],
        ];
    }
}
