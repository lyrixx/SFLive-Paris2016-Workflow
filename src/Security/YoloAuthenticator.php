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
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class YoloAuthenticator extends AbstractAuthenticator implements LogoutSuccessHandlerInterface
{
    private UserProviderInterface $userProvider;

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function supports(Request $request): bool
    {
        return 'login' === $request->attributes->get('_route');
    }

    public function authenticate(Request $request): PassportInterface
    {
        $username = $request->attributes->get('username');

        return new Passport(
            new UserBadge($username, function ($username) {
                return $this->userProvider->loadUserByUsername($username);
            }),
            new PasswordCredentials('password'),
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($request->headers->get('referer', '/'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        throw new AccessDeniedHttpException($exception->getMessageKey(), $exception);
    }

    public function onLogoutSuccess(Request $request)
    {
        return new RedirectResponse($request->headers->get('referer', '/'));
    }
}
