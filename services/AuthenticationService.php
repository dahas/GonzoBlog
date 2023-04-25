<?php

namespace Gonzo\Service;

use Gonzo\Sources\ServiceBase;
use Hybridauth\Provider\Google;
use Hybridauth\User\Profile;

class AuthenticationService extends ServiceBase {

    private Google $GoogleOAuthAdapter;

    public function __construct(private array|null $options = [])
    {
        parent::__construct();

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

        $this->GoogleOAuthAdapter = new Google($config);
    }

    public function login(): void
    {
        // E2E Testing
        if ($_ENV['MODE'] === 'test') {
            $_SESSION['user'] = [
                "name" => $_ENV['TEST_CRED_NAME'],
                "email" => $_ENV['TEST_CRED_EMAIL'],
            ];
        } else {
            $this->GoogleOAuthAdapter->authenticate();
            $profile = $this->getUserProfile();
            $_SESSION['user'] = [
                "name" => $profile->displayName,
                "email" => $profile->email,
            ];
        }

        session_regenerate_id();
    }

    public function logout(): void
    {
        if ($_ENV['MODE'] !== 'test') {
            $this->GoogleOAuthAdapter->disconnect();
        }
        unset($_SESSION['user']);

        session_regenerate_id();
    }

    public function isLoggedIn(): bool
    {
        if ($_ENV['MODE'] === 'test' && isset($_SESSION['user'])) {
            return true;
        } else {
            return $this->GoogleOAuthAdapter->isConnected() ? true : false;
        }
    }

    public function isAdmin(): bool
    {
        return isset($_SESSION['user']) 
            && password_verify($_SESSION['user']['email'], $_ENV['ACCOUNT_HASH']);
    }

    public function isAuthorized(string $email): bool
    {
        return (isset($_SESSION['user']) 
            && strtolower($_SESSION['user']['email']) === strtolower($email)) 
            || $this->isAdmin();
    }

    public function getUserProfile(): Profile
    {
        return $this->GoogleOAuthAdapter->getUserProfile(); 

    }
}