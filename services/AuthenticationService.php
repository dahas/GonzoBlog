<?php

namespace Gonzo\Service;

use Gonzo\Sources\{Request, Response, Session};
use Hybridauth\Provider\Google;
use Hybridauth\User\Profile;

/**
 * This Service uses the Google OAuth API to login and authorize users.
 */
class AuthenticationService {

    private Google $GoogleOAuthAdapter;

    public function __construct(
        protected Request $request, 
        protected Response $response, 
        protected Session $session
    ) {
        if ($_ENV['MODE'] === 'prod') {
            $callback = $_ENV['PUBLIC_DOMAIN'] . "/Authenticate";
        } else {
            $callback = $_ENV['LOCAL_HOST'] . "/Authenticate";
        }

        $config = [
            'callback' => $callback,
            'keys' => [
                'id' => $_ENV['OAUTH_GOOGLE_CLIENT_ID'],
                'secret' => $_ENV['OAUTH_GOOGLE_CLIENT_SECRET']
            ]
        ];

        if ($_ENV['MODE'] !== 'test')
            $this->GoogleOAuthAdapter = new Google($config);
    }

    public function login(): void
    {
        // E2E Testing
        if ($_ENV['MODE'] === 'test') {
            $this->session->set('user', [
                "name" => $_ENV['TEST_CRED_NAME'],
                "email" => $_ENV['TEST_CRED_EMAIL'],
            ]);
        } else {
            $this->GoogleOAuthAdapter->authenticate();
            $profile = $this->getUserProfile();
            $this->session->set('user', [
                "name" => $profile->displayName,
                "email" => $profile->email,
            ]);
        }

        session_regenerate_id();
    }

    public function logout(): void
    {
        if ($_ENV['MODE'] !== 'test') {
            $this->GoogleOAuthAdapter->disconnect();
        }
        $this->session->unset('user');

        session_regenerate_id();
    }

    public function isLoggedIn(): bool
    {
        if ($_ENV['MODE'] === 'test' && $this->session->get('user')) {
            return true;
        } else {
            return $this->GoogleOAuthAdapter->isConnected() ? true : false;
        }
    }

    public function isAdmin(): bool
    {
        return $this->session->get('user') && password_verify($this->session->get('user')['email'], $_ENV['ACCOUNT_HASH']);
    }

    public function isAuthorized(string $email): bool
    {
        return ($this->session->get('user') && strtolower($this->session->get('user')['email']) === strtolower($email)) || $this->isAdmin();
    }

    public function getUserProfile(): Profile
    {
        return $this->GoogleOAuthAdapter->getUserProfile(); 

    }
}