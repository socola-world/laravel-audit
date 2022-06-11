<?php

require_once 'vendor/autoload.php';

$projectDir = getcwd();
$laravelAuditDir = __DIR__;

shell_exec("cd {$projectDir}");

$fileChanges = shell_exec('git diff origin/master --name-only --diff-filter=A');
//$fileChanges = shell_exec('git diff ticket-coupon --name-only --diff-filter=AM');
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

//echo shell_exec("{$laravelAuditDir}\\vendor\\bin\\php-cs-fixer.bat --config={$laravelAuditDir}/.php_cs.php fix {$fileChangesString}");
$bladeFileChangesString = "resources/**/*.blade.php";
//echo "{$laravelAuditDir}\\node_modules\\.bin\\blade-formatter {$bladeFileChangesString} --w --wrap 999999999999";
echo shell_exec("{$laravelAuditDir}\\node_modules\\.bin\\blade-formatter {$bladeFileChangesString} --w --wrap 999999999999");
//echo shell_exec("{$laravelAuditDir}\\node_modules\\.bin\\blade-formatter resources/admin/**/*.blade.php --w --wrap 999999999999");
//echo "{$laravelAuditDir}\\node_modules\\.bin\\blade-formatter resources/admin/**/*.blade.php --w --wrap 999999999999";



//vendor\socoladaica\laravel-audit\node_modules\.bin\blade-formatter resources/**/*.blade.php --w --wrap 999999999999


//    php ..\socola-cms-demo-v2\cms-dev\laravel-audit\fix-phpcs.php
//    php ..\labs\laravel-audit\fix-phpcs.php
//..lab\node_modules\\.bin\\blade-formatter --w --wrap 999999999999
//..\lab\node_modules\.bin\blade-formatter --w --wrap 999999999999 resources/views/store_manager/tournaments/applyers/index.blade.php

//..\labs\laravel-audit\node_modules\.bin\blade-formatter --w --wrap 999999999999 resources\views\store_manager\tournaments\applyers\index.blade.php
