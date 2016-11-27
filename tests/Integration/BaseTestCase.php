<?php

namespace Tests\Integration;

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;

/**
 * This is an example class that shows how you could set up a method that
 * runs the application. Note that it doesn't cover all use-cases and is
 * tuned to the specifics of this skeleton app, so if your needs are
 * different, you'll need to change it.
 */
class BaseTestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * [$app description]
     * @var [type]
     */
    protected $app;

    /**
     *
     * @var type 
     */
    protected $ci;

    /**
     * [$capsule description]
     * @var [type]
     */
    protected $capsule;

    /**
     * Use middleware when running application?
     *
     * @var bool
     */
    protected $withMiddleware = true;

    /**
     *
     * @var type 
     */
    protected $token;

    /**
     * [setUp description]
     */
    public function setUp()
    {
        // Use the application settings
        $settings = require CONFIG . '/settings_test.php';

        $this->capsule = new \Illuminate\Database\Capsule\Manager;
        $this->capsule->addConnection($settings['settings']['db']);
        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();

        // Instantiate the application
        $this->app = $app       = new App($settings);
        $this->ci  = $app->getContainer();

        // Set up dependencies
        $withMiddleware = $this->withMiddleware;
        require CONFIG . '/bootstrap.php';
    }

    /**
     * Process the application given a request method and URI
     *
     * @param string $requestMethod the request method (e.g. GET, POST, etc.)
     * @param string $requestUri the request URI
     * @param array|object|null $requestData the request data
     * @return \Slim\Http\Response
     */
    public function request($requestMethod, $requestUri, $requestData = null, $env = [])
    {
        // Create a mock environment for testing with
        $environment = Environment::mock([
            'REQUEST_METHOD'     => $requestMethod,
            'REQUEST_URI'        => $requestUri
        ] + $env);

        // Set up a request object based on the environment
        $request = Request::createFromEnvironment($environment);

        // Add request data, if it exists
        if (isset($requestData)) {
            $request = $request->withParsedBody($requestData);
        }

        // Set up a response object
        $response = new Response();

        // Process the application
        $response = $this->app->process($request, $response);

        // Return the response
        return $response;
    }
    
    /**
     * Get table
     * 
     * @param string $name
     * @return type
     */
    public function table($name)
    {
        // get table class instance
        $className = "Tests\\Db\\{$name}";
        return call_user_func($className . '::getInstance');
    }

    /**
     * check isValidMd5
     * 
     * @param  string  $md5 [description]
     * @return boolean      [description]
     */
    protected function _isValidMd5($md5 = '')
    {
        return (boolean) preg_match('/^[a-f0-9]{32}$/', $md5);
    }

    /**
     * expectMd5String description]
     * 
     * @return [type] [description]
     */
    public function assertIsMd5String($md5 = '', $message = '')
    {
        $this->assertEquals(true, $this->_isValidMd5($md5), $message);
    }

}
