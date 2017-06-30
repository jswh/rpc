<?php
namespace RPC;
class Application {
    public function __construct($namespace) {
        $this->parser = new DefaultParser($namespace);
        $this->registerMetas();
    }

    public function registerMetas() {
        $metas = [
            'needLogin' => '.*',
            'httpMethod' => 'POST|GET',
            'cacheTime' => '\d+'
        ];
        foreach ($metas as $n => $p) {
            Annotation::registerMeta($n, $p);
        }
    }

    public function run() {
        $path = current(explode('?', $_SERVER['REQUEST_URI']));
        $procedure = $this->parser->parse($path);
        $annotation = new Annotation($procedure->getClass(), $procedure->method);
        $method = $annotation->meta('httpMethod');
        if ($method == "GET") {
            $params = $_GET;
        } else {
            $params = array_merge($_POST, $_GET);
        }

        return $procedure->call($params);
    }
}