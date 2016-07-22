<?php
//caoweijie@baixing.com
namespace RPC;

class Explorer {
    public function getClasses($rootDir) {
        $files = array_slice(scandir($rootDir), 2);

        return array_map(function ($item) {
            return explode('.', $item)[0];
        }, $files);
    }

    public function getFunctions($namespace, $className) {
        $re = [];
        $reflect = new \ReflectionClass($namespace . '\\' . $className);
        foreach ($reflect->getMethods() as $method) {
            if ($method->isPublic()) {
                $re[] = $method->name;
            }
        }

        return $re;
    }

    public function getParams($namespace, $className, $functionName) {
        $reflect = new \ReflectionClass($namespace . '\\' . $className);
        $method = $reflect->getMethod($functionName);
        $re = [];
        foreach ($method->getParameters() as $p) {
            $re[] = [
                'name' => $p->name,
                'default' => $p->isDefaultValueAvailable() ? $p->getDefaultValue() : '',
            ];
        }

        return $re;
    }
}
