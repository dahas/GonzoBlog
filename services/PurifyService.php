<?php

namespace Gonzo\Service;

use Gonzo\Sources\attributes\Inject;
use Gonzo\Sources\ServiceBase;
use Gonzo\Service\AuthenticationService;
use Gonzo\Sources\{Request, Response, Session};
use \HTMLPurifier_Config;
use \HTMLPurifier;

class PurifyService extends ServiceBase {

    #[Inject(AuthenticationService::class)]
    protected $auth;

    private HTMLPurifier $purifier;

    public function __construct(
        protected Request $request, 
        protected Response $response, 
        protected Session $session
    ) {
        parent::__construct($this->request, $this->response, $this->session);

        $config = HTMLPurifier_Config::createDefault();

        if ($this->auth->isAdmin()) {
            $config->set('HTML.Trusted', true);
            $config->set('Filter.YouTube', true);
        } else {
            $config->set('HTML.ForbiddenElements', ['img', 'iframe', 'a', 'script']);
        }

        $this->purifier = new HTMLPurifier($config);
    }

    public function purify(string $value): string
    {
        return $this->purifier->purify($value);
    }
}