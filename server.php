<?php


use App\Authentication\Authenticator;
use App\Authentication\GetIdController;
use App\Authentication\GetUserById;
use App\Authentication\Info\GetInfoById;
use App\Authentication\Info\UpdateInfo;
use App\Authentication\JwtDecode;
use App\Authentication\SignInController;
use App\Authentication\SignUpController;
use App\Authentication\Storage as Users;
use App\Categories\Controller\CreateCategory;
use App\Categories\Controller\GetAllCategories;
use App\Categories\Controller\GetCategoryById;
use App\Categories\Controller\UpdateCategory;
use App\Core\ErrorHandler;
use App\Core\JsonRequestDecoder;
use App\Core\Router;
use App\Core\Uploader;
use App\Categories\Storage as Categories;
use App\Records\Controller\CreateRecord;
use App\Records\Controller\GetAllRecords;
use App\Records\Controller\GetRecordById;
use App\StaticFiles\Controller as StaticFilesController;
use App\StaticFiles\Webroot;
use Dotenv\Dotenv;
use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use React\EventLoop\Factory;
use React\Filesystem\Filesystem;
use React\Http\Server;
use Sikei\React\Http\Middleware\CorsMiddleware;
use App\Records\Storage as Records;


require 'vendor/autoload.php';

$env = Dotenv::create(__DIR__);
$env->load();

$loop = Factory::create();
$mysql = new \React\MySQL\Factory($loop);
$uri = getenv('DB_LOGIN') . ':' . getenv('DB_PASS') . '@' . getenv('DB_HOST') . '/' . getenv('DB_NAME');
$connection = $mysql->createLazyConnection($uri);


$filesystem = Filesystem::create($loop);
$uploader = new Uploader($filesystem, __DIR__);

$categories = new Categories($connection);
$routes = new RouteCollector(new Std(), new GroupCountBased());



$routes->get('/uploads/{file:.*\.\w+}', new StaticFilesController(new Webroot($filesystem, __DIR__)));

$users = new Users($connection);
$authenticator = new Authenticator($users, getenv('JWT_KEY'));
$jwtDecode = new JwtDecode(getenv('JWT_KEY'));
$routes->post('/auth/signup', new SignUpController($users));
$routes->post('/auth/signin', new SignInController($authenticator));
$routes->post('/auth/getid', new GetIdController($jwtDecode));
$routes->get('/user', new GetUserById($users));
$routes->get('/auth/getinfo/{id:\d+}', new GetInfoById($users));
$routes->put('/info/{id:\d+}', new UpdateInfo($users));


$routes->post('/categories', new CreateCategory($categories, $users));
$routes->put('/categories/{id:\d+}', new UpdateCategory($categories));
$routes->get('/categories/{id:\d+}', new GetAllCategories($categories));
$routes->get('/category/{id:\d+}', new GetCategoryById($categories));

$records = new Records($connection);
$routes->post('/records', new CreateRecord($categories, $records));
$routes->get('/records/{id:\d+}', new GetAllRecords($records));
$routes->get('/record/{id:\d+}', new GetRecordById($records));

$settings = [
    'allow_origin'      => ['*'],
    'allow_headers'     => ['DNT','X-Custom-Header','Keep-Alive','User-Agent','X-Requested-With','If-Modified-Since','Cache-Control','Content-Type','Content-Range','Range'],
    'allow_methods'     => ['GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTIONS'],
];
$middleware = [new CorsMiddleware($settings), new ErrorHandler(), new JsonRequestDecoder(), new Router($routes)];
$server = new Server($middleware);
$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);

$server->listen($socket);

$server->on('error', function (Throwable $error) {
    echo 'Error: ' . $error->getMessage() . PHP_EOL;
});

echo 'Listening on ' . str_replace('tcp', 'http', $socket->getAddress()) . PHP_EOL;

$loop->run();
