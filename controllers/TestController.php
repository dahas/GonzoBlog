<?php declare(strict_types=1);

namespace Gonzo\Controller;

use Gonzo\Sources\attributes\Route;
use Gonzo\Sources\{Request, Response};

class TestController extends CommentsController {

    protected string $page = 'Test';
    protected string $route = '/Test';
    protected string $templateFile = 'Test.partial.html';

    public function __construct(protected Request $request, protected Response $response)
    {
        parent::__construct($request, $response);

        $this->template->assign([
            'title' => "A Page With Comments"
        ]);
    }

    #[Route(path: '/Test', method: 'get')]
    public function main(): void
    {
        parent::renderComments();
    }

    #[Route(path: '/Test/Comments/create', method: 'post')]
    public function createComment(): void
    {
        parent::createComment();
    }

    #[Route(path: '/Test/Comments/edit/{commentId}', method: 'get')]
    public function editComment(): void
    {
        parent::editComment();
    }

    #[Route(path: '/Test/Comments/update/{commentId}', method: 'post')]
    public function updateComment(): void
    {
        parent::updateComment();
    }

    #[Route(path: '/Test/Comments/hide/{commentId}', method: 'get')]
    public function hideComment(): void
    {
        parent::hideComment();
    }

    #[Route(path: '/Test/Comments/delete/{commentId}', method: 'get')]
    public function deleteComment(): void
    {
        parent::deleteComment();
    }

    #[Route(path: '/Test/Reply/{commentId}', method: 'get')]
    public function reply(): void
    {
        parent::reply();
    }

    #[Route(path: '/Test/Reply/create/{commentId}', method: 'post')]
    public function createReply(): void
    {
        parent::createReply();
    }

    #[Route(path: '/Test/Reply/edit/{commentId}/{replyId}', method: 'get')]
    public function editReply(): void
    {
        parent::editReply();
    }

    #[Route(path: '/Test/Reply/update/{commentId}/{replyId}', method: 'post')]
    public function updateReply(): void
    {
        parent::updateReply();
    }

    #[Route(path: '/Test/Reply/hide/{commentId}/{replyId}', method: 'get')]
    public function hideReply(): void
    {
        parent::hideReply();
    }

    #[Route(path: '/Test/Reply/delete/{commentId}/{replyId}', method: 'get')]
    public function deleteReply(): void
    {
        parent::deleteReply();
    }
}