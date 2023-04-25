<?php

namespace Gonzo\Service;

use Gonzo\Entities\CommentEntity;
use Gonzo\Entities\ReplyEntity;
use Gonzo\Service\DbalService;
use Gonzo\Service\AuthenticationService;
use Gonzo\Sources\attributes\Inject;
use Gonzo\Sources\ServiceBase;

class CommentsService extends ServiceBase {

    #[Inject(DbalService::class)]
    protected $dbal;

    #[Inject(AuthenticationService::class)]
    protected $auth;

    private string $tmplFile = 'Comments.partial.html';
    private $orm;

    public function __construct(private array|null $options = [])
    {
        parent::__construct();
        $this->orm = $this->dbal->getEntityManager();
    }

    public function readAll(string $route): array
    {
        $query = $this->orm->query(CommentEntity::class);
        $query->where('route')->is($route);
        if (!$this->auth->isLoggedIn()) {
            $query->andWhere('hidden')->is(0);
        }
        $query->orderBy('created', 'desc');
        return $query->all();
    }

    public function read(int $id): ?CommentEntity
    {
        return $this->orm->query(CommentEntity::class)
            ->find($id);
    }

    public function create(string $route, array $data): int
    {
        if ($this->auth->isLoggedIn()) {
            $comment = $this->orm->create(CommentEntity::class)
                ->setName($_SESSION['user']['name'])
                ->setEmail($_SESSION['user']['email'])
                ->setRoute($route)
                ->setComment($data['comment']);
            $this->orm->save($comment);
            return $comment->id();
        }
        return -1;
    }

    public function updateComment(array $data): int
    {
        if ($this->auth->isLoggedIn()) {
            $comment = $this->orm->query(CommentEntity::class)
                ->find($data['commentId']);
            if ($this->auth->isAuthorized($comment->getEmail())) {
                $comment->setComment($data['comment']);
                $this->orm->save($comment);
                return $comment->id();
            }
            return 0;
        }
        return -1;
    }

    public function hide(int $id): int
    {
        if ($this->auth->isLoggedIn()) {
            $comment = $this->orm->query(CommentEntity::class)
                ->find($id);
            if ($this->auth->isAuthorized($comment->getEmail())) {
                $hidden = $comment->getHidden() === 1 ? 0 : 1;
                $comment->setHidden($hidden);
                $this->orm->save($comment);
                return $comment->id();
            }
            return 0;
        }
        return -1;
    }

    public function delete(int $id): int
    {
        if ($this->auth->isLoggedIn()) {
            $comment = $this->orm->query(CommentEntity::class)
                ->find($id);
            if ($this->auth->isAuthorized($comment->getEmail())) {
                $this->orm->delete($comment);
                return $id;
            }
            return 0;
        }
        return -1;
    }

    public function getReply(int $id): ReplyEntity
    {
        return $this->orm->query(ReplyEntity::class)
            ->find($id);
    }

    public function createReply(array $data): int
    {
        if ($this->auth->isLoggedIn()) {
            $reply = $this->orm->create(ReplyEntity::class)
                ->setName($_SESSION['user']['name'])
                ->setEmail($_SESSION['user']['email'])
                ->setCommentID((int) $data['commentId'])
                ->setReply($data['comment']);
            $this->orm->save($reply);
            return $reply->id();
        }
        return -1;
    }

    public function updateReply(array $data): int
    {
        if ($this->auth->isLoggedIn()) {
            $reply = $this->orm->query(ReplyEntity::class)
                ->find($data['replyId']);
            if ($this->auth->isAuthorized($reply->getEmail())) {
                $reply->setReply($data['comment']);
                $this->orm->save($reply);
                return $reply->id();
            }
            return 0;
        }
        return -1;
    }

    public function hideReply(int $id): int
    {
        if ($this->auth->isLoggedIn()) {
            $reply = $this->orm->query(ReplyEntity::class)
                ->find($id);
            if ($this->auth->isAuthorized($reply->getEmail())) {
                $hidden = $reply->getHidden() === 1 ? 0 : 1;
                $reply->setHidden($hidden);
                $this->orm->save($reply);
                return $reply->id();
            }
            return 0;
        }
        return -1;
    }

    public function deleteReply(int $id): int
    {
        if ($this->auth->isLoggedIn()) {
            $reply = $this->orm->query(ReplyEntity::class)
                ->find($id);
            if ($this->auth->isAuthorized($reply->getEmail())) {
                $this->orm->delete($reply);
                return $id;
            }
            return 0;
        }
        return -1;
    }
}