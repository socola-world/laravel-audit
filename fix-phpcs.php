<?php

require_once 'vendor/autoload.php';

$projectDir = getcwd();
$laravelAuditDir = __DIR__;

shell_exec("cd {$projectDir}");

$fileChanges = shell_exec('git diff master --name-only --diff-filter=A');
//
//var_dump($fileChanges);
//die();
//A
$fileChanges = trim($fileChanges);
$fileChanges = explode("\n", $fileChanges);

$fileChangesString = implode(' ', $fileChanges);

$bladeFileChanges = collect($fileChanges)
    ->filter(fn ($e) => \Illuminate\Support\Str::endsWith($e, '.blade.php'))
    ->values()
    ->toArray()
;

$fileChanges = str_replace("\n", ' ', $fileChanges);
$bladeFileChangesString = implode(" ", $bladeFileChanges);
//$bladeFileChangesString = $bladeFileChanges[0];

echo shell_exec("{$laravelAuditDir}\\vendor\\bin\\php-cs-fixer.bat --config={$laravelAuditDir}/.php_cs.php fix {$fileChangesString}");

//echo "{$laravelAuditDir}\\node_modules\\.bin\\blade-formatter {$bladeFileChangesString} --w --wrap 999999999999";
echo shell_exec("{$laravelAuditDir}\\node_modules\\.bin\\blade-formatter {$bladeFileChangesString} --w --wrap 999999999999");
//echo shell_exec("{$laravelAuditDir}\\node_modules\\.bin\\blade-formatter resources/admin/**/*.blade.php --w --wrap 999999999999");
//echo "{$laravelAuditDir}\\node_modules\\.bin\\blade-formatter resources/admin/**/*.blade.php --w --wrap 999999999999";



//vendor\socoladaica\laravel-audit\node_modules\.bin\blade-formatter resources/**/*.blade.php --w --wrap 999999999999


//    php ..\socola-cms-demo-v2\cms-dev\laravel-audit\fix-phpcs.php
