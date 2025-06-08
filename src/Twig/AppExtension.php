<?php

namespace App\Twig;

use App\Service\AuthService;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    private AuthService $authService;
    private RequestStack $requestStack;

    public function __construct(AuthService $authService, RequestStack $requestStack)
    {
        $this->authService = $authService;
        $this->requestStack = $requestStack;
    }

    public function getGlobals(): array
    {
        $session = $this->requestStack->getSession();

        return [
            'currentUser' => $this->authService->getCurrentUser($session),
        ];
    }
}
