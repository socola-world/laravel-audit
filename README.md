# Laravel Audit

## Instalation

```shell
composer require socoladaica/laravel-audit
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
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\Tests"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\Tests\\App\\Http\\ControllersTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\Tests\\App\\Http\\RequestsTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\Tests\\App\\ModelsTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\Tests\\Common\\ClassTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\Tests\\Database\\DatabaseTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\Tests\\Database\\MigrationsTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\Tests\\EnvTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\Tests\\Psr\\Psr1Test"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\Tests\\Resources\\SCSSTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\Tests\\Resources\\ViewsTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\Tests\\RouteTest"
```

## Php-cs-fixer

```shell
vendor\bin\php-cs-fixer.bat --config=.php_cs.php fix
vendor\bin\php-cs-fixer.bat --config=vendor/socoladaica/laravel-audit/.php_cs.php fix
```

## Coming Soon

- **Request**
  - test missing addCustomValues -> **done**
    - date_from addCustomValues today, yesterday
  - rule missing type -> **done**
  - attributeShouldNotExists
  - instead, rule (between instead min max)
  - follow tyle digits_between
  - exists 
    - missing soft delete
    - model not Exists
  - duplicate rule
- **Model**
  - relation using pivot class instead table string -> **done**
  - column name snake_case -> **done**
  - split big model
  - pivot name
  - test relation foregin key
  - relation should be index
  - using pivot instead table string
  - mising cast
- **Migrattion**
  - test can roll back -> **done**
  - test migrate match db design -> **done**
  - test foregin key
- **Cast**
- **Routes**
  - route kebab-case -> **done**
  - dont use closure callback
  - use FormRequest instead Request
- **resources**
- **resources/lang**
  - key snake_case
- **resources/assets**
  - dùng `mix.scripts` thay cho `mix.copy`
- **resources/views**
  - use `{{ URL::asset() }}` instead `{!! URL::asset() !!}`
- **database**
  - column should not null -> **done**
  - column should be unsigned -> **done**
  - column should not negative
- **Other**
  - remove all todo
  - add log delete transaction
  - document.on insteam element.{event}
  - custom 404
    - https://codepen.io/knolcoder/pen/ZEewZaY
    - https://stackoverflow.com/questions/35774500/how-to-remove-focus-from-a-button-after-botstrap-modal-closed
    - https://stackoverflow.com/questions/30322918/bootstrap-modal-restores-button-focus-on-close
    - check css support
    - tìm hiểu về zerowith character

## Develop

**composer.json**
```json
{
    "requred-dev": {
      "socoladaica/laravel-audit": "dev-develop"
    },
    "repositories": [
        {
            "type": "path",
            "url": "../socola-cms-demo-v2/cms-dev/laravel-audit"
        }
    ]
}
```

```php
"repositories": [
    {
        "type": "path",
        "url": "../labs/laravel-audit"
    }
]
```

```shell
composer require socoladaica/laravel-audit:dev-develop --dev
```
