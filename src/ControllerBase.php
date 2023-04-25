<?php declare(strict_types=1);

namespace Gonzo\Sources;

class ControllerBase extends ServiceBase {

    protected Session $session;

    public function __construct()
    {
        parent::__construct();
        $this->session = new Session();
    }
}