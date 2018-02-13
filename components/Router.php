<?php

namespace App\components;

use App\errors\FileNotFoundException;

/**
 * Class Router
 *
 * @package App\components
 */
class Router
{
    /**
     * @var mixed|null array of routes defined as empty
     */
    private $routes = null;
    /**
     *
     * @var array request url
     */
    private $path = [];

    /**
     * Router constructor. Includes the site routes.
     */
    public function __construct()
    {
        $this->routes = require ROOT.'/config/routes.php';
    }

    /**
     * Retrieves uri from the user request
     *
     * @return string|null request uri
     */
    private function getURI(): ?string
    {
        return (!empty($_SERVER['REQUEST_URI'])) ? trim($_SERVER['REQUEST_URI'], '/'): null;
    }

    /**
     * Creates the request handler.
     * At the beginning, checks, whether uri doesn't have the regex expression within. If so, just splits it.
     * Otherwise, substitutes the data, following the regex expression and splits it.
     *
     * @param string $uriPattern uri pattern for routes searching(route key)
     * @param string $path route, which equals to pattern (route value)
     * @param string $url formed url
     * @return void
     */
    private function makePath(string $uriPattern, string $path, string $url): void
    {
        if(empty($uriPattern)) {
           $this->path = explode("/", $path);
        } else {
            $internalRoute = preg_replace("~$uriPattern~", $path, $url);
            $this->path = explode("/", $internalRoute);
        }
    }

    /**
     * Builds the action name like "SiteController"
     *
     * @return string controller name
     */
    private function makeControllerName(): string
    {
        return ucfirst(array_shift($this->path)).'Controller';
    }

    /**
     * Builds the action name like "actionIndex"
     *
     * @return string action name
     */
    private function makeActionName(): string
    {
        return 'action'.ucfirst(array_shift($this->path));
    }

    /**
     * Builds the controller class name with the specified namespace
     *
     * @param string $controller Controller class name
     * @return string controller name with namespace
     */
    private function makeControllersNamespace(string $controller): string
    {
        return "App\\controllers\\".$controller;
    }

    /**
     * Includes the controller class file whether exists. Otherwise, throws exception
     *
     * @param string $controllerName controller name
     * @throws FileNotFoundException whether the file not found
     * @return void
     */
    private function checkControllerFile(string $controllerName): void
    {
        $file = ROOT."/controllers/{$controllerName}.php";
        if (!file_exists($file)) {
            throw new FileNotFoundException("File {$file} not found");
        }

        require $file;
    }
    /**
     * Calls special controller and its action with the action arguments
     *
     * @param object $controllerObj controller object
     * @param string $actionName controller method(action)
     * @param array $options method parameters
     * @return bool
     */
    private function executeRequest(object $controllerObj, string $actionName, array $options): bool
    {
        call_user_func_array([$controllerObj, $actionName], $options);
    }
    /**
     * Searches the matches between the user request and site routes by regex expression.
     * If matches, builds the action, controller and params, whether necessary.
     * Breaks the operation whether all the data handled, calling the particular controller, action and its params in advance.
     *
     * @throws
     * @return void
     */
    public function run(): void
    {
        // Getting the user request
        $url = ($this->getURI() !== false)? $this->getURI(): false;
        // Laying out the site routes as request pattern AND request handlers
        foreach ($this->routes as $uriPattern => $path) {
            // Looking for matches between routes and user request
            if (preg_match("~$uriPattern~", $url)) {
                // Creating the request handler
                $this->makePath($uriPattern, $path, $url);
                // Building the controller class name
                $controllerName = $this->makeControllerName();
                // Building the action name
                $actionName = $this->makeActionName();
                // Retrieving the arguments for the action
                $options = $this->path;
                // Checking, whether controllers exists
                $this->checkControllerFile($controllerName);
                // Building the controller class name adding the namespace
                $controllerName = $this->makeControllersNamespace($controllerName);
                // Creating the controller object
                $controllerObj = new $controllerName;
                // Calling the formed handler
                $this->executeRequest($controllerObj, $actionName, $options);
                break;
                }
        }
    }
}
