<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public function authenticate(Request $request): Passport
    {
        // Récupère correctement les champs du formulaire Symfony
        $data = $request->request->all('login_form');

        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password)
        );
    }


    public function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('app_login');
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?\Symfony\Component\HttpFoundation\Response
    {
        return new \Symfony\Component\HttpFoundation\RedirectResponse('/');
    }
}