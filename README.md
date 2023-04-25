# Gonzo Blog

<img src="https://img.shields.io/badge/PHP-8.1.2-orange" /> <img src="https://img.shields.io/badge/Latte-3.x-green" /> <img src="https://img.shields.io/badge/Opis ORM-1.x-yellow" /> <img src="https://img.shields.io/badge/PHPUnit-10.x-blue" />

- *Gonzo Blog* is a mixture of static pages and dynamic content. 
- Blog articles can be created and edited directly in the browser without leaving the page.
- *Gonzo Blog* requires some good knowledge in web development.
- *Gonzo Blog* is Open Source.

## Minimum Requirements

- PHP 8.1.2
- A database
- Composer
- A Google Account

## Installation
````
$ git clone https://github.com/dahas/GonzoBlog.git .
$ composer install
````

# Setting things up

## Environment variables

Rename `.env.example` to `.env`. All sensitive informations are stored in this file. Double check that it is added to `.gitignore` so it won't accidentially appear in your public repository.

Open the `.env` file and adjust some settings:

- Leave the LOCAL_HOST setting as it is.
- Set your PUBLIC_DOMAIN if you have one already registered.

## Become the Blog admin. 

In the terminal run:
````
$ php encrypt.php <your_gmail_address>
````
- Copy the hash and assign it to ACCOUNT_HASH. 

## Create a MySQL Database

- Import `gonzoblog.sql`.
- Provide your Database credentials.

## Enable Google User Authentication

All kind of authentication and authorization is done externally using the Google OAuth service. Therefore it is neccessary that you have a Google account and have the API enabled. Below is a description of how to enable the Authentication API.

1. In the Google Cloud Console go to API credentials:  
https://console.developers.google.com/apis/credentials?hl=de
1. Create a new Project
1. Click on "Configure Consent Screen", choose "External".
1. Enter a name and provide your email address.
1. Skip "Scopes" and "Test Users" and finish the configuration.
1. Go back to Credentials, click on "Create Credentials" and choose "OAuth Client ID".
1. Select "Web Application" as application type.
1. Copy the LOCAL_HOST and the PUBLIC_DOMAIN from your `.env` file, paste them into authorised redirect URIs and extend both of them with *"/Authenticate"*.  
    E. g.: http://localhost:2400/Authenticate
1. Save it.
1. Copy and paste the Client ID and Secret from the final screen into the `.env` file.

## Register TinyMCE Editor

As the administrator of the Blog you are able to write and edit articles with a "What You See Is What You Get" editor.

1. Open https://www.tiny.cloud/.
1. Register your public Domain.
1. Copy and paste the API Key.  

# Run Gonzo Blog

## Run locally

Launch the web server:
````
$ php -S localhost:2400 -t public
````

# How to

## Extend Gonzo with Controllers

With a Controller you can extend Gonzo with new Pages and new Functionality.

Create a file `YourController.php` in the `controllers` directory:

````php
// controllers/YourController.php

<?php declare(strict_types=1);

namespace Gonzo\Controller;

use Gonzo\Sources\attributes\Route;
use Gonzo\Sources\{ControllerBase, Request, Response};

class YourController extends ControllerBase {

    private array $data;

    public function __construct(
        protected Request $request, 
        protected Response $response)
    {
        $this->data = $this->request->getData();
    }

    #[Route(path: '/YourPath/{name}', method: 'get')]
    public function main(): void
    {
        $this->response->write("Hello " . $this->data['name']);
    }
    
    //...
}
````
Check it out in the web browser (provide your name):  
http://localhost:2400/YourPath/<YourName\>

### Render HTML

Now, if you want to render a beautiful HTML template, you need a Template Engine. The Latte Engine is available as a Service. 

Here is how you use it:

Create an HTML file named `Your.layout.html` with the following content in the `templates` folder:  

````html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
</head>
<body style="font-family: 'Courier New', Courier, monospace; margin: 60px auto; text-align: center">
    <div style="background-color: rgb(196, 250, 255); padding: 20px 0;">
        <h1>{$header}</h1>
        <p>{$message}</p>
    </div>
</body>
</html>
````
Inject the Template Engine as shown below:  

````php
// controllers/YourController.php

<?php declare(strict_types=1);

namespace Gonzo\Controller;

use Gonzo\Service\TemplateEngine;
use Gonzo\Sources\attributes\{Inject, Route};
use Gonzo\Sources\{ControllerBase, Request, Response};

class YourController extends ControllerBase {

    #[Inject(TemplateEngine::class)]
    protected $template;

    protected array $data;

    public function __construct(
        protected Request $request, 
        protected Response $response)
    {
        $this->data = $this->request->getData();
    }

    #[Route(path: '/YourPath/{name}', method: 'get')]
    public function main(): void
    {
        $this->injectServices();

        $this->template->assign([
            'title' => 'Your Controller',
            'header' => 'Gonzo is cool!',
            'message' => 'But ' . $this->data['name'] . ' is way cooler :p'
        ]);
        $this->template->parse('Your.layout.html');
        $this->template->render($this->request, $this->response);
    }
    
    //...
}
````
Check it out again:  
http://localhost:2400/YourPath/<YourName\>

## Extend the Menu

Finally, you may want your new page to appear in the navigation bar. Therefore open `menu.js` and insert the following lines right after the "/Blog" section:

````
// ...
{
    "path": "/YourPath/<YourName\>",
    "controller": "YourController",
    "title": "Welcome Message",
    "enabled": true
},
// ...
````

## Extend Gonzo with Services
In addition to installing libraries with Composer, you can create your own Services. To do this, you simply create a Class in the `services` directory and inject it via Attributes in the Classes where you need the Service. 

Below is a template of a Service class. The constructor with an array of options is mandatory, although using options is optional.

````php
// lib/YourService.php

<?php declare(strict_types=1);

namespace Gonzo\Service;

class YourService {

    public function __construct(private array|null $options = [])
    {

    }
    ...
}
````
Here is how you inject Services in another Class. Note how the constructor triggers the parent constructor:
````php
// controllers/AnyController.php

<?php declare(strict_types=1);

namespace Gonzo\Controller;

use Gonzo\Sources\ControllerBase;
use Gonzo\Service\{YourService, AnotherService};

class AnyController extends ControllerBase {

    /**
     * Service without Options:
     */
    #[Inject(YourService::class)]
    protected $yourService;

    /**
     * Service with Options:
     */
    #[Inject(AnotherService::class, [
        "opt1" => "Option 1", 
        "opt2" => "Option 2"
    ])]
    protected $anotherService;
    
    public function __construct()
    {
        parent::__construct();
    }

    // ...
}
````
It is also possible to use Services in a Service. Therefore the Service must inherit from the ServiceBase. Like so:
````php
// lib/YourService.php

<?php declare(strict_types=1);

namespace Gonzo\Service;

use Gonzo\Sources\ServiceBase;
use Gonzo\Service\AnyService;

class YourService extends ServiceBase {

    #[Inject(AnyService::class)]
    protected $anyService;

    public function __construct(private array|null $options = [])
    {
        parent::__construct();
    }
    
    // ...
}
````

# Available Services 
There are some Services already available which you can use and/or modify to your needs.

## ArticlesService
### *Dependencies*: 
* Services: `AuthenticationService`, `DbalService`, `PurifyService`
* Templates: `Article.partial.html`
### *Description*:  
Create and edit Blog articles.

## AuthenticationService
### *Description*:  
Use this service to authenticate and authorize users to post comments and/or write Blog articles.

## CommentsService
### *Dependencies*: 
* Services: `AuthenticationService`, `DbalService`, `MarkdownService`
* Controllers: `CommentsController`
* Entities: `CommentsEntity`, `RepliesEntity`
* Templates: `Comments.partial.html`
### *Description*:  
Add a commentary feature to a page. Users can add comments and reply to them.
  
## DbalService
### *Description*:  
A database abstraction layer. Gonzo uses the DBAL and ORM from Opis. Check it out here: https://opis.io/orm/1.x/quick-start.html

## MarkdownService
### *Dependencies*:
* Services: `PurifyService`
### *Description*:  
Creates and renders HTML from Markdown.

## MenuService
### *Dependencies*:
* Templates: `Menu.partial.html`
### *Description*:  
Creates and renders the menu inside the navigation bar according to the specification in `menu.json`.

## PurifyService
### *Description*:  
Uses HTMLPurifier to remove malicious code.

## TemplateService
### *Description*:  
Parses HTML templates. The Template Engine is build upon the Latte library. Learn more about Latte here: https://latte.nette.org/en/guide


