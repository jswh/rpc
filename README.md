# Make Things Simple 

## installation
    composer require jswh/rpc
## quick start with default application
### index.php
``` php
    <?php
    require __DIR__ . '/vendor/autoload.php';
    $app = new \RPC\Application('Api');
    echo $app->run();
```
### your api file
```php
    <?php
    namesapce Api;
    
    class Hello
    {
        /**
         * @httpMethod GET
         * @param string $name
         * @return void
         */
        public function hello($name) {
            return 'Hello ' . $name . ' !'
        }
    }
```
### start application
    php -S localhost:8000 index.php
### call api
    http://localhost:8000/Hello.hello?name=world
## write your own
### procedure parser
```php
    <?php
    class MyParser implements RPC\interfaces\ProcedureParser {
        public function parse($path) {
            preg_match("/(\w+)\.(\w+)$/", $_SERVER['REQUEST_URI'], $matches);
            if (count($matches) !== 3) {
                return null;
            }
            $p = new Procedure('MyApi', $matches[1], $matches[2]);
    
            return $p;
        }
    }
```
### logic
```php
    <?php
    Annotation::registerMeta('method', 'GET|PUT|POST');
    $parser = new MyParser
    $procedure = $parser->parse(null);
    $annotation = new Annotation($procedure->getClass(), $procedure->method);
    $method = $annotation->meta('method');
    if ($method && $method !== $_SERVER['HTTP_METHOD']) {
        header('', true, 404);
    } else {
        if ($method === "GET") {
            $params = $_GET;
        } else {
            $params = array_merge($_POST, $_GET);
        }
        header('Content-Type: application/json');

        return json_encode($procedure->call($params));
    }
```
