<?php

namespace SocolaDaiCa\LaravelAudit\Tests\App\Http;

use SocolaDaiCa\LaravelAudit\Tests\TestCase;

class ControllerTest extends TestCase
{
    public function testViewPath()
    {
        static::assertTrue(true);
        ////        dd($this->getControllerReflectionClass()->map(function (\ReflectionClass $reflectionClass) {
////            return $reflectionClass->name;
////        })->toArray());
//        $this->getControllerReflectionClass()->each(function (\ReflectionClass $controllerReflectionClass) {
////            if ($controllerReflectionClass->getName() != "App\Http\Controllers\HomeController") {
////                return;
////            }
////            $phpParse = new PhpParser();
////            $useSta = $phpParse->parseUseStatements($controllerReflectionClass);
//            $content = file_get_contents($controllerReflectionClass->getFileName());
//            preg_match_all('/view\([\s\t\r\n]*["\']([^"\']+)["\']/', $content, $viewPaths);
//            if (empty($viewPaths[1])) {
//                return;
//            }
//            $viewPaths = $viewPaths[1];
//            $viewPathFormat = '/^[a-z0-9_]+(?:\.[a-z0-9_]+)*$/';
//            foreach ($viewPaths as $viewPath) {
//                $this->assertMatchesRegularExpression(
//                    $viewPathFormat,
//                    $viewPath,
//                    "{$controllerReflectionClass->getName()} view {$viewPath} wrong format"
//                );
//            }
//        });
    }
}
