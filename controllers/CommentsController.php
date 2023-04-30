<?php declare(strict_types=1);

namespace Gonzo\Controller;

use Gonzo\Service\CommentsService;
use Gonzo\Sources\attributes\Inject;
use Gonzo\Sources\{Request, Response};

class CommentsController extends AppController {

    #[Inject(CommentsService::class)]
    protected $comments;

    protected string $route;
    protected string $templateFile;

    public function __construct(
        protected Request $request,
        protected Response $response
    ) {
        if (!isset($this->route) || !$this->route) {
            throw new \Gonzo\Sources\exceptions\InvalidConfigException(
                "Protected parameter '\$route' missing! Must be set in child class to overwrite parent setting."
            );
        }

        if (!isset($this->templateFile) || !$this->templateFile) {
            throw new \Gonzo\Sources\exceptions\InvalidConfigException(
                "Protected parameter '\$templateFile' missing! Must be set in child class to overwrite parent setting."
            );
        }

        parent::__construct($request, $response);

        $this->template->assign([
            "expanded" => false,
        ]);
    }

    public function renderComments(): void
    {
        $text = '';
        if ($this->session->issetTemp()) {
            $tmpData = $this->session->getTempData($this->request->getUri());
            $text = $tmpData['comment'];
            $this->session->unsetTemp();
        }

        $this->template->assign([
            "comments" => $this->comments->readAll($this->route),
            'route' => $this->route,
            "expanded" => !empty($text) || ($this->data['expanded'] ?? false),
            "text" => $text
        ]);
        $this->template->parse($this->templateFile);
        $this->template->render($this->request, $this->response);
    }

    public function reply(): void
    {
        if (!$this->auth->isLoggedIn()) {
            $this->session->setRedirect($this->request->getUri());
            $this->auth->login();
        }

        $text = '';
        if ($this->session->issetTemp()) {
            $tmpData = $this->session->getTempData($this->request->getUri());
            $this->data['commentId'] = $tmpData['commentId'];
            $text = $tmpData['comment'];
            $this->session->unsetTemp();
        }

        $comment = $this->comments->read((int) $this->data['commentId']);

        if ($comment) {
            $this->template->assign([
                'isReply' => true,
                'route' => $this->route,
                'form_header' => 'Reply to #' . $this->data['commentId'],
                'commentId' => $this->data['commentId'],
                'text' => $text,
                "comments" => [$comment]
            ]);
            $this->template->parse($this->templateFile);
            $this->template->render($this->request, $this->response);
        } else {
            $this->response->redirect("/PageNotFound");
        }
    }

    public function editComment(): void
    {
        if (!$this->auth->isLoggedIn()) {
            $this->session->setRedirect($this->request->getUri());
            $this->auth->login();
        }

        $text = '';
        if ($this->session->issetTemp()) {
            $tmpData = $this->session->getTempData($this->request->getUri());
            $this->data['commentId'] = $tmpData['commentId'];
            $text = $tmpData['comment'];
            $this->session->unsetTemp();
        }

        $comment = $this->comments->read((int) $this->data['commentId']);

        if ($comment) {
            if (!$this->auth->isAuthorized($comment->getEmail())) {
                $this->response->redirect("/PermissionDenied");
            }

            $this->template->assign([
                'isUpdate' => true,
                'route' => $this->route,
                'form_header' => 'Edit Comment #' . $this->data['commentId'],
                'commentId' => $this->data['commentId'],
                "name" => $comment->getName(),
                "email" => $comment->getEmail(),
                "text" => $text ? $text : $comment->getComment(),
                "comments" => [$comment]
            ]);
            $this->template->parse($this->templateFile);
            $this->template->render($this->request, $this->response);
        } else {
            $this->response->redirect("/PageNotFound");
        }
    }

    public function editReply(): void
    {
        if (!$this->auth->isLoggedIn()) {
            $this->session->setRedirect($this->request->getUri());
            $this->auth->login();
        }

        $text = '';
        if ($this->session->issetTemp()) {
            $tmpData = $this->session->getTempData($this->request->getUri());
            $this->data = $tmpData;
            $text = $tmpData['comment'];
            $this->session->unsetTemp();
        }

        $comment = $this->comments->read((int) $this->data['commentId']);

        if ($comment) {
            $reply = $this->comments->getReply((int) $this->data['replyId']);

            if (!$this->auth->isAuthorized($reply->getEmail())) {
                $this->response->redirect("/PermissionDenied");
            }

            $this->template->assign([
                'isReplyUpdate' => true,
                'route' => $this->route,
                'form_header' => 'Edit Reply #' . $reply->id(),
                'commentId' => $reply->getCommentID(),
                'replyId' => $reply->id(),
                "name" => $reply->getName(),
                "email" => $reply->getEmail(),
                "text" => $text ? $text : $reply->getReply(),
                "comments" => [$comment]
            ]);
            $this->template->parse($this->templateFile);
            $this->template->render($this->request, $this->response);
        } else {
            $this->response->redirect("/PageNotFound");
        }
    }

    public function createComment(): void
    {
        $id = $this->comments->create($this->route, $this->data);
        if ($id < 0) { // Not logged in!
            $this->session->setTempData($this->route, $this->data);
            $this->auth->login();
        }
        $this->response->redirect("{$this->route}#comments");
    }

    public function updateComment(): void
    {
        $id = $this->comments->updateComment($this->data);
        if ($id < 0) { // Not logged in!
            $this->session->setTempData("{$this->route}/Comments/edit/
                {$this->data['commentId']}", $this->data);
            $this->auth->login();
        } else if ($id == 0) {
            $this->response->redirect("/PermissionDenied");
        }
        $this->response->redirect("{$this->route}#C{$id}");
    }

    public function hideComment(): void
    {
        $id = $this->comments->hide((int) $this->data['commentId']);
        if ($id < 0) { // Not logged in!
            $this->session->setRedirect($this->request->getUri());
            $this->auth->login();
        } else if ($id == 0) {
            $this->response->redirect("/PermissionDenied");
        }
        $this->response->redirect("{$this->route}#comments");
    }

    public function deleteComment(): void
    {
        $id = $this->comments->delete((int) $this->data['commentId']);
        if ($id < 0) { // Not logged in!
            $this->session->setRedirect($this->request->getUri());
            $this->auth->login();
        } else if ($id == 0) {
            $this->response->redirect("/PermissionDenied");
        }
        $this->response->redirect("{$this->route}#comments");
    }

    public function createReply(): void
    {
        $id = $this->comments->createReply($this->data);
        if ($id < 0) { // Not logged in!
            $this->session->setTempData("{$this->route}/Reply/
                {$this->data['articleId']}", $this->data);
            $this->auth->login();
        }
        $this->response->redirect("{$this->route}#R{$id}");
    }

    public function updateReply(): void
    {
        $id = $this->comments->updateReply($this->data);
        if ($id < 0) { // Not logged in!
            $this->session->setTempData("{$this->route}/Reply/edit/
                {$this->data['articleId']}", $this->data);
            $this->auth->login();
        } else if ($id == 0) {
            $this->response->redirect("/PermissionDenied");
        }
        $this->response->redirect("{$this->route}#R{$id}");
    }

    public function hideReply(): void
    {
        $id = $this->comments->hideReply((int) $this->data['replyId']);
        if ($id < 0) { // Not logged in!
            $this->session->setRedirect($this->request->getUri());
            $this->auth->login();
        } else if ($id == 0) {
            $this->response->redirect("/PermissionDenied");
        }
        $this->response->redirect("{$this->route}#R{$id}");
    }

    public function deleteReply(): void
    {
        $id = $this->comments->deleteReply((int) $this->data['replyId']);
        if ($id < 0) { // Not logged in!
            $this->session->setRedirect($this->request->getUri());
            $this->auth->login();
        } else if ($id == 0) {
            $this->response->redirect("/PermissionDenied");
        }
        $this->response->redirect("{$this->route}#C{$this->data['commentId']}");
    }
}