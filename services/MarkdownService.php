<?php

namespace Gonzo\Service;

use Gonzo\Sources\attributes\Inject;
use Gonzo\Service\PurifyService;
use Gonzo\Sources\ServiceBase;
use \Parsedown;

class MarkdownService extends ServiceBase {

    #[Inject(PurifyService::class, [
        'HTML.ForbiddenElements' => ['img', 'iframe', 'a', 'script']
    ])]
    protected $purifier;

    private Parsedown $parsedown;

    public function __construct(private array|null $options = [])
    {
        parent::__construct();

        $escaped = $options['escaped'] ?? false;

        $this->parsedown = new Parsedown();
        $this->parsedown->setMarkupEscaped($escaped);
    }

    public function parse(string $value): string
    {
        $markdown = $this->parsedown->text($value); 
        return $this->purifier->purify($markdown);
    }
}