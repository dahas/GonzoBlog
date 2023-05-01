<?php declare(strict_types=1);

namespace Gonzo\Controller;

use Gonzo\Sources\attributes\Route;

class IndexController extends AppController {

    #[Route(path: ['/', '/Index'], method: 'get')]
    public function main(): void
    {
        $this->template->assign([
            'title' => 'Home' # <-- Overwrite the {$title} marker in App.layout.html
        ]);

        $this->template->parse('Index.partial.html');
        $this->template->render($this->request, $this->response);
    }
}