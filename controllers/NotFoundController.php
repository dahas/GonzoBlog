<?php declare(strict_types=1);

namespace Gonzo\Controller;

use Gonzo\Sources\attributes\Route;
use Gonzo\Sources\Request;
use Gonzo\Sources\Response;

class NotFoundController extends AppController {

    public function __construct(protected Request $request, protected Response $response)
    {
        parent::__construct($request, $response);
    }
    
    #[Route(path: '/PageNotFound', method: 'get')]
    public function main(): void
    {
        $this->template->assign([
            'title' => 'Error 404 - Page Not Found'
        ]);
        $this->template->parse('404.partial.html');
        $this->template->render($this->request, $this->response);
    }
}