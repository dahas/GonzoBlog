<?php declare(strict_types=1);

namespace Gonzo\Controller;

use Gonzo\Service\ArticlesService;
use Gonzo\Sources\attributes\{Inject, Route};
use Gonzo\Sources\{Request, Response};

class ArticleController extends CommentsController {

    #[Inject(ArticlesService::class, ['hello' => "world"])]
    protected $articles;

    protected string $route = '/Blog/Article/%d';
    protected string $templateFile = 'Article.partial.html';

    public function __construct(protected Request $request, protected Response $response)
    {
        parent::__construct($request, $response);

        $articles = $this->articles->readAll();

        if (isset($this->data['articleId']) && $this->data['articleId']) {
            $article = $this->articles->read((int) $this->data['articleId']);
        } else {
            $article = $articles[0];
        }

        $this->route = sprintf($this->route, $this->data['articleId']);

        if ($article) {
            $this->template->assign([
                'title' => $article->getTitle(),
                'keywords' => $article->getKeywords(),
                'article' => $article,
                'articles' => $articles,
                'referer' => $this->request->getReferer(),
                'currentArticle' => $article->id(),
            ]);
        } else {
            $this->response->redirect("/PageNotFound");
        }
    }

    public static function extractPreviewImage($key, $articleHtml): string 
    {
        $doc = new \DOMDocument();
        @$doc->loadHTML($articleHtml);

        $tags = $doc->getElementsByTagName('img');

        return $tags && $tags->item(0) ? $tags->item(0)->getAttribute('src') : "/assets/imgs/blog/random{$key}.jpg";
    }

    #[Route(path: '/Blog/Article/{articleId}', method: 'get')]
    public function read(): void
    {
        parent::renderComments();
    }

    #[Route(path: '/Blog/Article/{articleId}/Comments/create', method: 'post')]
    public function createComment(): void
    {
        parent::createComment();
    }

    #[Route(path: '/Blog/Article/{articleId}/Comments/edit/{commentId}', method: 'get')]
    public function editComment(): void
    {
        parent::editComment();
    }

    #[Route(path: '/Blog/Article/{articleId}/Comments/update/{commentId}', method: 'post')]
    public function updateComment(): void
    {
        parent::updateComment();
    }

    #[Route(path: '/Blog/Article/{articleId}/Comments/hide/{commentId}', method: 'get')]
    public function hideComment(): void
    {
        parent::hideComment();
    }

    #[Route(path: '/Blog/Article/{articleId}/Comments/delete/{commentId}', method: 'get')]
    public function deleteComment(): void
    {
        parent::deleteComment();
    }

    #[Route(path: '/Blog/Article/{articleId}/Reply/{commentId}', method: 'get')]
    public function reply(): void
    {
        parent::reply();
    }

    #[Route(path: '/Blog/Article/{articleId}/Reply/create/{commentId}', method: 'post')]
    public function createReply(): void
    {
        parent::createReply();
    }

    #[Route(path: '/Blog/Article/{articleId}/Reply/edit/{commentId}/{replyId}', method: 'get')]
    public function editReply(): void
    {
        parent::editReply();
    }

    #[Route(path: '/Blog/Article/{articleId}/Reply/update/{commentId}/{replyId}', method: 'post')]
    public function updateReply(): void
    {
        parent::updateReply();
    }

    #[Route(path: '/Blog/Article/{articleId}/Reply/hide/{commentId}/{replyId}', method: 'get')]
    public function hideReply(): void
    {
        parent::hideReply();
    }

    #[Route(path: '/Blog/Article/{articleId}/Reply/delete/{commentId}/{replyId}', method: 'get')]
    public function deleteReply(): void
    {
        parent::deleteReply();
    }
}