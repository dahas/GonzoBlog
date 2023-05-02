<?php

namespace Gonzo\Service;

use Gonzo\Sources\attributes\Inject;
use Gonzo\Service\PurifyService;
use Gonzo\Sources\{Request, Response, Session};
use Gonzo\Sources\ServiceBase;
use \Parsedown;

class MarkdownService extends ServiceBase {

    #[Inject(PurifyService::class)]
    protected $purifier;

    private Parsedown $parsedown;

    public function __construct(
        protected Request $request, 
        protected Response $response, 
        protected Session $session
    ) {
        parent::__construct($this->request, $this->response, $this->session);

        $this->parsedown = new Parsedown();
        $this->parsedown->setMarkupEscaped(false);
    }

    public function parse(string $value): string
    {
        $markdown = $this->parsedown->text($value); 
        return $this->purifier->purify($markdown);
    }
}