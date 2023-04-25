<?php declare(strict_types=1);

namespace Gonzo\Controller;

use Gonzo\Service\ArticlesService;
use Gonzo\Sources\attributes\{Inject, Route};

class BlogController extends AppController {

    #[Inject(ArticlesService::class)]
    protected $articles;

    protected string $menuItem = 'Blog';
    protected string $templateFile = 'Article.partial.html';

    #[Route(path: ['/Blog', '/Blog/{offset}'], method: 'get')]
    public function main(): void
    {
        $offset = $this->data['offset'] ?? 1;
        $offset = (int) $offset;
        $limit = 6;
        $totalCount = 0;

        $articles = $this->articles->readAll($offset, $limit, $totalCount);
        
        $count = count($articles);

        $pagination = $this->renderPagination($offset, $limit, $totalCount);

        $this->template->assign([
            'title' => "Blog",
            'pagination' => $pagination,
            'articles' => $articles,
            'cols' => $count > 2 ? 3 : $count
        ]);

        $this->template->parse('Blog.partial.html');
        $this->template->render($this->request, $this->response);
    }

    private function renderPagination(int $offset, int $limit, int $count): string 
    {
        $range = ceil($count / $limit);

        if ($range > 1) {
            $this->template->assign([
                'range' => $range,
                'curr' => $offset,
                'prev' => $offset - 1,
                'next' => $offset + 1,
                'prevDisabled' => $offset == 1 ? true : false,
                'nextDisabled' => $range == $offset ? true : false,
            ]);
    
            $this->template->parse('Pagination.partial.html');
            return $this->template->getHtml();
        }

        return "";
    }
}