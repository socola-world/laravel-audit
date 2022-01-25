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

Make sure you run `composer dumpautoload before run any testcase`

```shell
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\Tests\\Http\\RequestTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\Tests\\Common\\ClassTest"

vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\Tests\\App\\Http\\RequestTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\Tests\\App\\ModelTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\Tests\\Routes\\RouteTest"

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

## Comming Soon

- **Request**
  - test missing addCustomValues
  - rule missing type
  - attributeShoundNotExists
  - instead rule (bettwen instead min max)
  - follow tyle digits_between
  - exists 
    - missing soft delete
    - model not Exists
- **Ccntroller**
  - use FormRequest instead Request
- **Model**
  - using pivot instead table string
- **Migrattion**
  - test can rollback
  - test migrate match db design
- test Cast
- test routes
- test resources
  - test lang
- test database
  - column should not null
  - column sould min zero
  - column should not negative
