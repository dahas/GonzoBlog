<?php declare(strict_types=1);

namespace Gonzo\Sources;

use Gonzo\Sources\interfaces\AppInterface;
use Gonzo\Sources\Request;
use Gonzo\Sources\Response;
use Gonzo\Sources\Router;

class Application implements AppInterface {

    public function execute(): void
    {
        session_name('sid');
        session_start();

        $request = new Request();
        $response = new Response();
        
        $router = new Router($request, $response);
        $router->notFound(function() {
            header("location: /PageNotFound");
            exit();
        });
        $router->run();

        $response->flush();
    }
}