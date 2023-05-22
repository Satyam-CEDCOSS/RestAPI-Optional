<?php

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Http\Response;

$loader = new Loader();
$loader->registerNamespaces(
    [
        'MyApp\Models' => __DIR__ . '/models/',
    ]
);

require_once  __DIR__ . "/vendor/autoload.php";

$loader->register();

$container = new FactoryDefault();

$container->set(
    'manager',
    function () {
        return new Phalcon\Mvc\Collection\Manager();
    }
);
$container->set(
    'mongo',
    function () {
        $mongo = new MongoDB\Client(
            "mongodb+srv://root:Password123@mycluster.qjf75n3.mongodb.net/?retryWrites=true&w=majority"
        );

        return $mongo->api;
    },
    true
);

$app = new Micro($container);

$app->get(
    '/api/movie',
    function () {
        $movies = $this->mongo->data->find();

        $data = [];

        foreach ($movies as $movie) {
            $data[] = [
                'id'   => $movie->_id,
                'name' => $movie->name,
                'type' => $movie->type,
                'year' => $movie->year,
            ];
        }

        echo json_encode($data);
    }
);

$app->get(
    '/api/movie/search/{name}',
    function ($name) {
        $movies = $this->mongo->data->find(array('name' => $name));
        $data = [];
        foreach ($movies as $movie) {
            $data[] = [
                'id'   => $movie->_id,
                'name' => $movie->name,
                'type' => $movie->type,
                'year' => $movie->year,
            ];
        }

        echo json_encode($data);
    }
);

$app->get(
    '/api/movie/{id:[0-9]+}',
    function ($id) {
        $movies = $this->mongo->data->findOne(array('_id' => new MongoDB\BSON\ObjectId((int)$id)));
        $data = [];
        foreach ($movies as $movie) {
            $data[] = [
                'id'   => $movie->_id,
                'name' => $movie->name,
                'type' => $movie->type,
                'year' => $movie->year,
            ];
        }
        echo json_encode($data);
    }
);

$app->post(
    '/api/movie}',
    function () use ($app) {
        $robot = $app->request->getJsonRawBody();
        $data[] = [
            'id'   => $robot->_id,
            'name' => $robot->name,
            'type' => $robot->type,
            'year' => $robot->year,
        ];
        $this->mongo->data->insertMany($data);
        echo json_encode($data);
    }
);

$app->post(
    '/api/movie',
    function () use ($app) {
        $robot = $app->request->getJsonRawBody();
        $data[] = [
            'id'   => $robot->_id,
            'name' => $robot->name,
            'type' => $robot->type,
            'year' => $robot->year,
        ];
        $this->mongo->data->insertMany($data);
        echo json_encode($data);
    }
);

$app->put(
    '/api/movie/{id:[0-9]+}',
    function ($id) use ($app) {
        $robot = $app->request->getJsonRawBody();
        $data[] = [
            'id'   => $robot->_id,
            'name' => $robot->name,
            'type' => $robot->type,
            'year' => $robot->year,
        ];
        $this->mongo->data->updateOne(array("_id" => new MongoDB\BSON\ObjectId((int)$id)), array('$set' => $data));
        echo json_encode($data);
    }
);

$app->delete(
    '/api/movie/{id:[0-9]+}',
    function ($id) {
        $this->mongo->data->deleteOne(array('_id' => new MongoDB\BSON\ObjectId((int)$id)));
        echo "Deletion Successful";
    }
);

$app->handle(
    $_SERVER["REQUEST_URI"]
);
