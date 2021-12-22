# Laravel Audit

## Instalation

```shell
composer require socoladaica/laravel-audit

composer require socoladaica/laravel-audit:dev-main
```

## Update phpunit.xml

add `testsuite` into `testsuites`
```xml
<testsuite name="Audit">
    <directory suffix="Test.php">./vendor/socoladaica/laravel-audit/tests</directory>
</testsuite>
```


```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="./vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Audit">
            <directory suffix="Test.php">./vendor/socoladaica/laravel-audit/tests</directory>
        </testsuite>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>

        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    <filter>
        <whitelist processUncoveredFilesFromWhitelist="true">
            <directory suffix=".php">./app</directory>
        </whitelist>
    </filter>
    <php>
        <server name="APP_ENV" value="testing"/>
        <server name="BCRYPT_ROUNDS" value="4"/>
        <server name="CACHE_DRIVER" value="array"/>
        <server name="DB_CONNECTION" value="sqlite"/>
        <server name="DB_DATABASE" value=":memory:"/>
        <server name="MAIL_DRIVER" value="array"/>
        <server name="QUEUE_CONNECTION" value="sync"/>
        <server name="SESSION_DRIVER" value="array"/>
    </php>
</phpunit>

```

```shell
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\Tests"
```

## TestCase

```shell
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\Tests\\Http\\RequestTest"

 vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\Tests\\App\\Http\\RequestTest"

```

```php
"repositories": [
    {
        "type": "path",
        "url": "../socola-cms-demo-v2/cms-dev/laravel-audit"
    }
]

"repositories": [
    {
        "type": "path",
        "url": "../labs/laravel-audit"
    }
]
```

```
"socoladaica/laravel-audit": "dev-develop"
```

```scss
 $app->loadEnvironmentFrom('.env');
```

## Shell

```shell
vendor\bin\php-cs-fixer.bat --config=.php_cs.php fix
vendor\bin\php-cs-fixer.bat --config=vendor/socoladaica/laravel-audit/.php_cs.php fix
```
