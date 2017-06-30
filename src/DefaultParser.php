<?php


namespace RPC;


use RPC\interfaces\ProcedureParser;

class DefaultParser implements ProcedureParser
{
    public function __construct($api)
    {
        $this->api = $api;
    }

    public function parse($path) {
        preg_match("/(\w+)\.(\w+)$/", $path, $matches);
        if (count($matches) !== 3) {
            return null;
        }
        $p = new Procedure($this->api, $matches[1], $matches[2]);

        return $p;
    }

}
