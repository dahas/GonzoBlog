<?php declare(strict_types=1);

namespace Gonzo\Controller;

use Gonzo\Service\{TemplateService, MenuService, AuthenticationService};
use Gonzo\Sources\attributes\{Inject, Route};
use Gonzo\Sources\{ControllerBase, Request, Response};

class AppController extends ControllerBase {

    #[Inject(TemplateService::class)]
    protected $template;

    #[Inject(MenuService::class)]
    protected $menu;

    #[Inject(AuthenticationService::class)]
    protected $auth;

    protected array $data;
    protected bool $isLoggedIn;
    protected bool $isAdmin;

    public function __construct(protected Request $request, protected Response $response)
    {
        parent::__construct();

        $this->data = $this->request->getData();

        $this->isLoggedIn = $this->auth->isLoggedIn();
        $this->isAdmin = $this->auth->isAdmin();

        $this->template->assign([
            "title" => "Gonzo - A Blog Framework",
            "keywords" => "Blog, Framework",
            "description" => "Gonzo - A Blog Framework",
            "e2eTestMode" => $_ENV['MODE'] === 'test',
            "nav" => $this->menu->getItems(),
            "user" => $this->auth->isLoggedIn() ? $_SESSION['user'] : [],
            "isLoggedIn" => $this->isLoggedIn,
            "isAdmin" => $this->isAdmin,
            "currentPath" => "/" . $this->request->getSegment(0)
        ]);
    }

    /**
     * Redirects to Googles OAuth Client and back when successfully authenticated.
     * 
     * Note: If you change the routes path below, you have to change the URI
     * in the Google Cloud OAuth Client API as well as in .env file!
     */
    #[Route(path: '/Authenticate', method: 'get')]
    public function authenticate(): void
    {
        // Store referer and avoid refering to Google:
        if (!isset($this->data['state'])) {
            $this->session->setRedirect($this->request->getReferer());
        }

        if ($this->auth->isLoggedIn()) {
            $this->auth->logout();
        } else {
            $this->auth->login();
        }

        $path = "/";
        if ($this->session->issetRedirect()) {
            $path = $this->session->getRedirect();
            $this->session->unsetRedirect();
        }
        if ($this->session->issetTemp()) {
            $path = $this->session->getTempRoute();
        }

        $this->response->redirect($path);
    }
}