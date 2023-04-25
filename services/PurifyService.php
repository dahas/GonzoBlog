<?php

namespace Gonzo\Service;

use \HTMLPurifier_Config;
use \HTMLPurifier;

class PurifyService {

    private HTMLPurifier $purifier;

    public function __construct(private array|null $options = [])
    {
        $config = HTMLPurifier_Config::createDefault();
        foreach($options as $option => $value) {
            $config->set($option, $value);
        }
        $this->purifier = new HTMLPurifier($config);
    }

    public function purify(string $value): string
    {
        return $this->purifier->purify($value);
    }
}