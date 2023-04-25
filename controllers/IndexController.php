<?php declare(strict_types=1);

namespace Gonzo\Controller;

use Gonzo\Sources\attributes\Route;
use Gonzo\Sources\{Request, Response};

class IndexController extends AppController {

    public function __construct(protected Request $request, protected Response $response)
    {
        parent::__construct($request, $response);

        $this->template->assign([
            'title' => 'Gonzo - The framework',
            'header' => 'Gonzo - Yet another framework',
            "subtitle" => "Gonzo is the numeronym of the word 'framework'. Use this lightweight framework to quickly build feature rich web applications with PHP. If you are unfamiliar or 
            inexperienced with developing secure and high-performance web applications, I strongly recommend using Symfony, Laravel, or a similar well tested product."
        ]);
    }

    #[Route(path: ['/', '/Index'], method: 'get')]
    public function main(): void
    {
        $this->template->parse('Index.partial.html');
        $this->template->render($this->request, $this->response);
    }
}