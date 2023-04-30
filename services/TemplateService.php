<?php declare(strict_types=1);

namespace Gonzo\Service;

use Gonzo\Service\MarkdownService;
use Gonzo\Sources\attributes\Inject;
use Latte\Engine;
use Gonzo\Sources\{Request, Response, Session};
use Gonzo\Sources\ServiceBase;

class TemplateService extends ServiceBase {

    #[Inject(MarkdownService::class)]
    protected $markdown;

    private string $templateDir = ROOT . '/templates';
    private string $cacheDir = ROOT . '/.latte/cache';

    private Engine $latte;
    private array $templateVars = [];
    private string $html = "";

    public function __construct(
        protected Request $request, 
        protected Response $response, 
        protected Session $session
    ) {
        parent::__construct($this->request, $this->response, $this->session);

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0775, true);
        }

        $this->latte = new Engine();
        $this->latte->setTempDirectory($this->cacheDir);
        $this->latte->setAutoRefresh($_ENV['MODE'] !== 'prod');

        // Additional Custom Filter for MarkdownService:
        $this->latte->addFilter('markdown', fn(string $s) => $this->markdown->parse($s));
    }

    public function assign(array $_vars): void
    {
        $this->templateVars = array_merge($this->templateVars, $_vars);
    }

    /**
     * Use this method to parse a template.
     * 
     * @param string $file The file to be parsed
     * @param string|null $block A defined block within the template (optional)
     * @return string HTML
     */
    public function parse(string $file, string|null $block = null): void
    {
        $this->html = $this->latte->renderToString($this->templateDir . '/' . $file, $this->templateVars, $block);
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    public function render(): void
    {
        $this->response->addHeader("Content-Type", "text/html");
        $this->response->write($this->html);
    }

    public function setTemplateDir(string $dir): void
    {
        $this->templateDir = $dir;
    }

    public function setCacheDir(string $dir): void
    {
        $this->cacheDir = $dir;
    }
}