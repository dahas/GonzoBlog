<?php declare(strict_types=1);

namespace Gonzo\Sources;

use Gonzo\Sources\attributes\Route;

class Router {

    private Request $request;
    private Response $response;
    private array $handlers;
    private $notFoundHandler;

    private const GET = "get";
    private const POST = "post";

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;

        $cache = new Cache();

        if ($cache->has("routes") && $_ENV["MODE"] !== "dev") {
            $this->handlers = $cache->get("routes");
        } else {
            $files = array_diff(scandir(ROOT . "/controllers"), array('.', '..'));

            foreach ($files as $file) {
                $controller = "Gonzo\\Controller\\" . explode(".", $file)[0];
                $reflectionController = new \ReflectionClass($controller);

                foreach ($reflectionController->getMethods() as $method) {
                    $attributes = $method->getAttributes(Route::class);

                    foreach ($attributes as $attribute) {
                        $route = $attribute->newInstance();

                        $methods = $route->getMethods();
                        foreach($route->getPaths() as $path) {
                            $this->handlers[$path] = [
                                "methods" => $methods,
                                "path" => $path,
                                "callback" => [$controller, $method->getName()]
                            ];
                        }
                    }
                }
            }
            $cache->set("routes", $this->handlers);
        }
    }

    public function notFound(callable|array $callback): void
    {
        $this->notFoundHandler = $callback;
    }

    public function run(): void
    {
        $route = $this->request->getRoute();
        $method = $this->request->getMethod();

        foreach ($this->handlers as $hdlr) {
            if ($this->match($route, $method, $hdlr, $params)) {
                $handler = [
                    'method' => $method,
                    'path' => $hdlr['path'],
                    'callback' => $hdlr['callback']
                ];

                if (!empty($params)) {
                    $this->request->setData($params);
                }

                if (is_array($handler["callback"]) && count($handler["callback"]) == 2) {
                    $callback = [
                        new $handler["callback"][0]($this->request, $this->response), 
                        $handler["callback"][1]
                    ];
                } else if (is_array($handler["callback"]) && count($handler["callback"]) != 2) {
                    $callback = null;
                } else {
                    $callback = $handler["callback"];
                }
    
                if ($callback) {
                    $res = call_user_func_array($callback, []);
                    if ($res === false) {
                        $this->response->setStatus(500);
                    }
                }
                return;
            }
        }

        if ($this->notFoundHandler) {
            $res = call_user_func_array($this->notFoundHandler, []);
            if ($res === false) {
                $this->response->setStatus(500);
            }
        } else {
            $this->response->setStatus(404);
        }
    }

    private function match(string $request, string $method, array $handler, ?array &$params = []): bool
    {
        $requestArr = explode('/', substr($request, 1));
        $pathArr = explode('/', substr($handler['path'], 1));

        if (count($requestArr) !== count($pathArr) || !in_array($method, $handler['methods'])) {
            return false;
        }

        foreach ($pathArr as $i => $segment) {
            if (isset($requestArr[$i])) {
                if (str_starts_with($segment, '{')) {
                    $parameter = explode(' ', preg_replace('/{([\w\-%]+)(<(.+)>)?}/', '$1 $3', $segment));
                    $paramName = $parameter[0];
                    $paramRegExp = (empty($parameter[1]) ? '[\w\-]+': $parameter[1]);
                    if (preg_match('/^' . $paramRegExp . '$/', $requestArr[$i])) {
                        $params[$paramName] = $requestArr[$i];
                        continue;
                    }
                } elseif ($segment === $requestArr[$i]) {
                    continue;
                }
            }
            return false;
        }
        return true;
    }
}