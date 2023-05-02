<?php

namespace Gonzo\Service;

use Gonzo\Sources\{Request, Response, Session};
use Gonzo\Entities\ArticleEntity;
use Gonzo\Service\{DbalService, AuthenticationService, PurifyService};
use Gonzo\Sources\attributes\Inject;
use Gonzo\Sources\ServiceBase;

class ArticlesService extends ServiceBase {

    #[Inject(DbalService::class)]
    protected $dbal;

    #[Inject(AuthenticationService::class)]
    protected $auth;

    #[Inject(PurifyService::class)]
    protected $purifier;

    private $orm;

    public function __construct(
        protected Request $request, 
        protected Response $response,
        protected Session $session
    ) {
        parent::__construct($this->request, $this->response, $this->session);
        
        $this->orm = $this->dbal->getEntityManager();
    }

    public function readAll(int $offset = 0, int $limit = 0, int &$count = 0): array
    {
        // Get totalcount for Pagination:
        $db = $this->dbal->getDatabase();
        $cQuery = $db->from('blog_articles');
        if (!$this->auth->isLoggedIn() || ($this->auth->isLoggedIn() && !$this->auth->isAdmin())) {
            $cQuery->where('hidden')->is(0);
        }
        $count = $cQuery->count();

        // Get Blog articles:
        $query = $this->orm->query(ArticleEntity::class);
        if (!$this->auth->isLoggedIn() || ($this->auth->isLoggedIn() && !$this->auth->isAdmin())) {
            $query->where('hidden')->is(0);
        }
        $query->orderBy('created', 'desc');
        if($offset > 0 && $limit > 0) {
            $query->offset($offset*$limit-$limit);
            $query->limit($limit);
        }
        return $query->all();
    }

    public function read(int $id): ?ArticleEntity
    {
        return $this->orm->query(ArticleEntity::class)
            ->find($id);
    }

    public function create(array $data): int
    {
        if ($this->auth->isLoggedIn()) {
            if ($this->auth->isAdmin()) {
                $articleText = $this->purifier->purify($data['articleText']);
                $article = $this->orm->create(ArticleEntity::class)
                    ->setTitle($data['title'] ?? "")
                    ->setDescription($data['description'] ?? "")
                    ->setImage($data['image'] ?? "")
                    ->setArticle($articleText)
                    ->setKeywords($data['keywords'])
                    ->setHidden($data['hidden']);
                $this->orm->save($article);
                return $article->id();
            }
            return 0;
        }
        return -1;
    }

    public function update(array $data): int
    {
        if ($this->auth->isLoggedIn()) {
            if ($this->auth->isAdmin()) {
                $articleText = $this->purifier->purify($data['articleText']);
                $article = $this->orm->query(ArticleEntity::class)
                    ->find($data['articleId'])
                    ->setTitle($data['title'] ?? "")
                    ->setDescription($data['description'] ?? "")
                    ->setImage($data['image'] ?? "")
                    ->setKeywords($data['keywords'] ?? "")
                    ->setHidden($data['hidden'] ?? "")
                    ->setArticle($articleText);
                $this->orm->save($article);
                return $article->id();
            }
            return 0;
        }
        return -1;
    }

    public function hide(int $id): int
    {
        if ($this->auth->isLoggedIn()) {
            if ($this->auth->isAdmin()) {
                $article = $this->orm->query(ArticleEntity::class)
                    ->find($id);
                $hidden = $article->getHidden() === 1 ? 0 : 1;
                $article->setHidden($hidden);
                $this->orm->save($article);
                return $article->id();
            }
            return 0;
        }
        return -1;
    }

    public function delete(int $id): int
    {
        if ($this->auth->isLoggedIn()) {
            if ($this->auth->isAdmin()) {
                $this->orm->query(ArticleEntity::class)
                    ->where('id')->is($id)
                    ->delete();
                return $id;
            }
            return 0;
        }
        return -1;
    }
}