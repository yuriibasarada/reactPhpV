<?php


use App\Authentication\Authenticator;
use App\Authentication\GetIdController;
use App\Authentication\GetInfoById;
use App\Authentication\JwtDecode;
use App\Authentication\SignInController;
use App\Authentication\SignUpController;
use App\Authentication\Storage as Users;
use App\Core\ErrorHandler;
use App\Core\JsonRequestDecoder;
use App\Core\Router;
use App\Core\Uploader;
use App\Orders\Controller\CreateOrder\Controller;
use App\Orders\Controller\DeleteOrder;
use App\Orders\Controller\GetAllOrders;
use App\Orders\Controller\GetOrderById;
use App\Orders\Storage as Orders;
use App\Products\Controller\CreateProduct;
use App\Products\Controller\DeleteProduct;
use App\Products\Controller\GetAllProducts;
use App\Products\Controller\GetProductById;
use App\Products\Controller\UpdateProduct;
use App\Products\Storage as Products;
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

require 'vendor/autoload.php';

$env = Dotenv::create(__DIR__);
$env->load();

$loop = Factory::create();
$mysql = new \React\MySQL\Factory($loop);
$uri = getenv('DB_LOGIN') . ':' . getenv('DB_PASS') . '@' . getenv('DB_HOST') . '/' . getenv('DB_NAME');
$connection = $mysql->createLazyConnection($uri);


$filesystem = Filesystem::create($loop);
$uploader = new Uploader($filesystem, __DIR__);

$products = new Products($connection);
$orders = new Orders($connection);
$routes = new RouteCollector(new Std(), new GroupCountBased());

$routes->get('/products', new GetAllProducts($products));
$routes->post('/products', new CreateProduct($products, $uploader));
$routes->get('/products/{id:\d+}', new GetProductById($products));
$routes->put('/products/{id:\d+}', new UpdateProduct($products));
$routes->delete('/products/{id:\d+}', new DeleteProduct($products));

$routes->get('/orders', new GetAllOrders($orders));
$routes->post('/orders', new Controller($orders, $products));
$routes->get('/orders/{id:\d+}', new GetOrderById($orders));
$routes->delete('/orders/{id:\d+}', new DeleteOrder($orders));

$routes->get('/uploads/{file:.*\.\w+}', new StaticFilesController(new Webroot($filesystem, __DIR__)));

$users = new Users($connection);
$authenticator = new Authenticator($users, getenv('JWT_KEY'));
$jwtDecode = new JwtDecode(getenv('JWT_KEY'));
$routes->post('/auth/signup', new SignUpController($users));
$routes->post('/auth/signin', new SignInController($authenticator));
$routes->post('/auth/getid', new GetIdController($jwtDecode));
$routes->get('/auth/getinfo/{id:\d+}', new GetInfoById($users));

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
