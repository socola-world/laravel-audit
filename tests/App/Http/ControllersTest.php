<?php

namespace SocolaDaiCa\LaravelAudit\Tests\App\Http;

use SocolaDaiCa\LaravelAudit\Tests\TestCase;

class ControllersTest extends TestCase
{
    public function testViewPath()
    {
        $this->getControllerReflectionClass()->each(function (\ReflectionClass $controllerReflectionClass) {
            $content = file_get_contents($controllerReflectionClass->getFileName());
            preg_match_all('/view\([\s\t\r\n]*["\']([^"\']+)["\']/', $content, $viewPaths);

            if (empty($viewPaths[1])) {
                return;
            }
            $viewPaths = $viewPaths[1];
            $viewPathFormat = '/^[a-z0-9_]+(?:\.[a-z0-9_]+)*$/';

            foreach ($viewPaths as $viewPath) {
                $this->assertMatchesRegularExpression(
                    $viewPathFormat,
                    $viewPath,
                    "{$controllerReflectionClass->getName()} view {$viewPath} wrong format"
                );
            }
        });
    }
}
