<?php declare(strict_types=1);

namespace Gonzo\Controller;

use Gonzo\Service\ArticlesService;
use Gonzo\Sources\attributes\{Inject, Route};

class EditorController extends AppController {

    #[Inject(ArticlesService::class)]
    protected $articles;

    #[Route(path: '/Editor', method: 'get')]
    public function main(): void
    {
        if (!$this->auth->isLoggedIn()) {
            $_SESSION['redirect'] = $this->request->getUri();
            $this->auth->login();
        }

        if (!$this->auth->isAdmin()) {
            $this->response->redirect("/PermissionDenied");
        }

        if ($this->session->issetTemp()) {
            $tmpData = $this->session->getTempData($this->request->getUri());
            $this->data = $tmpData;
            $this->session->unsetTemp();
        }

        $this->template->assign([
            'title' => "Create an Article",
            'tinymceApiKey' => $_ENV['TINYMCE_API_KEY'],
            'referer' => $this->request->getReferer(),
            'articleTitle' => $this->data['title'],
            'articleDescription' => $this->data['description'],
            'articleText' => $this->data['articleText'],
            'articleKeywords' => $this->data['keywords'],
            'articleHidden' => $this->data['hidden']
        ]);

        $this->template->parse('Editor.partial.html');
        $this->template->render($this->request, $this->response);
    }

    #[Route(path: '/Editor/edit/{articleId}', method: 'get')]
    public function edit(): void
    {
        if (!$this->auth->isLoggedIn()) {
            $_SESSION['redirect'] = $this->request->getUri();
            $this->auth->login();
        }

        if (!$this->auth->isAdmin()) {
            $this->response->redirect("/PermissionDenied");
        }

        if ($this->session->issetTemp()) {
            $tmpData = $this->session->getTempData($this->request->getUri());
            $this->data = $tmpData;
            $this->session->unsetTemp();

            $this->template->assign([
                'title' => "Edit an Article",
                'tinymceApiKey' => $_ENV['TINYMCE_API_KEY'],
                'referer' => $this->data['referer'],
                'articleId' => $this->data['articleId'],
                'articleTitle' => $this->data['title'],
                'articleDescription' => $this->data['description'],
                'articleText' => $this->data['articleText'],
                'articleKeywords' => $this->data['keywords'],
                'articleHidden' => $this->data['hidden']
            ]);
        } else {
            $article = $this->articles->read((int) $this->data['articleId']);

            $this->template->assign([
                'title' => "Edit an Article",
                'tinymceApiKey' => $_ENV['TINYMCE_API_KEY'],
                'referer' => $this->request->getReferer(),
                'articleId' => $article->id(),
                'articleTitle' => $article->getTitle(),
                'articleDescription' => $article->getDescription(),
                'articleText' => $article->getArticle(),
                'articleKeywords' => $article->getKeywords(),
                'articleHidden' => $article->getHidden()
            ]);
        }

        $this->template->parse('Editor.partial.html');
        $this->template->render($this->request, $this->response);
    }

    #[Route(path: '/Editor/create', method: 'post')]
    public function createArticle(): void
    {
        $id = $this->articles->create($this->data);
        if ($id < 0) {
            $this->session->setTempData("/Editor", $this->data);
            $this->auth->login();
        } else if ($id == 0) {
            $this->response->redirect("/PermissionDenied");
        }
        $this->response->redirect("/Blog/Article/{$id}");
    }

    #[Route(path: '/Editor/update', method: 'post')]
    public function updateArticle(): void
    {
        $id = $this->articles->update($this->data);
        if ($id < 0) {
            $this->session->setTempData("/Editor/edit/{$this->data['articleId']}", $this->data);
            $this->auth->login();
        } else if ($id == 0) {
            $this->response->redirect("/PermissionDenied");
        }
        $this->response->redirect($this->data['referer']);
    }

    #[Route(path: '/Editor/hide/{articleId}', method: 'get')]
    public function hideArticle(): void
    {
        $id = $this->articles->hide((int) $this->data['articleId']);
        if ($id < 0) {
            $_SESSION['redirect'] = $this->request->getReferer();
            $this->auth->login();
        } else if ($id == 0) {
            $this->response->redirect("/PermissionDenied");
        }
        $this->response->redirect($this->request->getReferer());
    }

    #[Route(path: '/Editor/delete/{articleId}', method: 'get')]
    public function deleteArticle(): void
    {
        $id = $this->articles->delete((int) $this->data['articleId']);
        if ($id < 0) {
            $_SESSION['redirect'] = $this->request->getReferer();
            $this->auth->login();
        } else if ($id == 0) {
            $this->response->redirect("/PermissionDenied");
        }
        $this->response->redirect("/Blog");
    }

    #[Route(path: '/Editor/upload', method: 'post')]
    public function upload(): void
    {
        if (!$this->auth->isAdmin()) {
            $this->response->redirect("/PermissionDenied");
        }

        $accepted_origins = [$_ENV['LOCAL_HOST'], $_ENV['PUBLIC_DOMAIN']];

        $imageFolder = ROOT . "/public/blog_files/";

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // same-origin requests won't set an origin. If the origin is set, it must be valid.
            if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
                $this->response->addHeader('Access-Control-Allow-Origin', $_SERVER['HTTP_ORIGIN']);
            } else {
                $this->response->setStatus(403, "Origin Denied");
                return;
            }
        }

        reset($_FILES);

        // echo "<pre>"; print_r($_FILES); echo "<pre>"; die;

        $temp = current($_FILES);
        if (is_uploaded_file($temp['tmp_name'])) {
            /*
            If your script needs to receive cookies, set images_upload_credentials : true in
            the configuration and enable the following two headers.
            */
            // header('Access-Control-Allow-Credentials: true');
            // header('P3P: CP="There is no P3P policy."');

            // Sanitize input
            if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
                $this->response->setStatus(400, "Invalid File Name");
                return;
            }

            // Verify extension
            if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
                $this->response->setStatus(400, "Invalid File Type");
                return;
            }

            $filetowrite = $imageFolder . $temp['name'];
            if (move_uploaded_file($temp['tmp_name'], $filetowrite)) {
                // Respond to the successful upload with JSON.
                // Use a location key to specify the path to the saved image resource.
                // { location : '/blog_files/file.ext'}
                $json = json_encode(array('location' => '/blog_files/' . $temp['name']));
                $this->response->setStatus(200);
                $this->response->addHeader("Content-Type", "application/json");
                $this->response->write($json);
            } else {
                $this->response->setStatus(500);
            }
        } else {
            $this->response->setStatus(500);
        }
    }
}