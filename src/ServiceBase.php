<?php declare(strict_types=1);

namespace Gonzo\Sources;
use Gonzo\Sources\traits\Injection;

class ServiceBase  {

    use Injection;

    public function __construct(protected Request $request, protected Response $response, protected Session $session)
    {
        $this->triggerServiceInjection($this->request, $this->response, $this->session);
    }
}