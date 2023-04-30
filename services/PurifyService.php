<?php

namespace Gonzo\Service;

use \HTMLPurifier_Config;
use \HTMLPurifier;

class PurifyService {

    private HTMLPurifier $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.ForbiddenElements', ['img', 'iframe', 'a', 'script']);
        $this->purifier = new HTMLPurifier($config);
    }

    public function purify(string $value): string
    {
        return $this->purifier->purify($value);
    }
}