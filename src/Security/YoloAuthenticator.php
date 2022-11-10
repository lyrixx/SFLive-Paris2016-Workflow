<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class YoloAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly UserProviderInterface $userProvider,
    ) {
    }

    public function supports(Request $request): bool
    {
        return 'login' === $request->attributes->get('_route');
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->attributes->get('username');

        return new SelfValidatingPassport(
            new UserBadge($username, $this->userProvider->loadUserByIdentifier(...)),
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse((string) $request->headers->get('referer', '/'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        throw new AccessDeniedHttpException($exception->getMessageKey(), $exception);
    }

    public function onLogoutSuccess(Request $request): Response
    {
        return new RedirectResponse((string) $request->headers->get('referer', '/'));
    }
}
