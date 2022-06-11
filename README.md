# Laravel Audit

## Instalation

```shell
composer require socoladaica/laravel-audit
```

## Update phpunit.xml

add `testsuite` into `testsuites`
```xml
<testsuite name="Audit">
    <directory suffix="Test.php">./vendor/socoladaica/laravel-audit/src/TestCases</directory>
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
            <directory suffix="Test.php">./vendor/socoladaica/laravel-audit/src/TestCases</directory>
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
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\TestCases"
```

## TestCase

Make sure you run `composer dumpautoload before run any testcase`

```shell
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\TestCases"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\TestCases\\App\\Http\\ControllersTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\TestCases\\App\\Http\\RequestsTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\TestCases\\App\\Models\\ModelTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\TestCases\\App\\Models\\PivotTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\TestCases\\Common\\ClassTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\TestCases\\ConfigsTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\TestCases\\Database\\DatabaseTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\TestCases\\Database\\MigrationsTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\TestCases\\EnvTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\TestCases\\Psr\\Psr1Test"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\TestCases\\Resources\\SCSSTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\TestCases\\Resources\\ViewsTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\TestCases\\RoutesTest"
vendor\bin\phpunit.bat --filter="SocolaDaiCa\\LaravelAudit\\TestCases\\DotFileTest"
```
## Code Fixer

### Php-cs-fixer

```shell
vendor\bin\php-cs-fixer.bat --config=.php_cs.php fix
vendor\bin\php-cs-fixer.bat --config=vendor/socoladaica/laravel-audit/.php_cs.php fix
..\labs\laravel-audit\vendor\bin\php-cs-fixer.bat --config=..\labs\laravel-audit\.php_cs.php fix
```

### Phpstan

create `phpstan.neon` in root of project

```neon
includes:
    - ./vendor/socoladaica/laravel-audit/phpstan.neon

parameters:

    paths:
        - app

    # The level 9 is the highest level
    level: 5

    ignoreErrors:
        - '#PHPDoc tag @var#'

    excludePaths:
        - ./*/*/FileToBeExcluded.php

    checkMissingIterableValueType: false

```

```shell
vendor\bin\phpstan analyse
```

### translations-checker 

```shell
php artisan translations:check
```

### blade-formatter

```
cd vendor\socoladaica\laravel-audit
npm i
cd ../../../
vendor\socoladaica\laravel-audit\node_modules\.bin\blade-formatter resources/**/*.blade.php --w --wrap 999999999999

vendor\socoladaica\laravel-audit\node_modules\.bin\blade-formatter resources/views/layouts/**/*.blade.php --w --wrap 999999999999
```

## Coming Soon

- **Request**
  - [x] test missing addCustomValues
    - [ ] date_from addCustomValues today, yesterday
  - [x] rule missing type
  - [ ] attributeShouldNotExists
  - [ ] instead, rule (between instead min max)
  - [ ] follow type digits_between
  - [ ] exists 
    - [ ] missing soft delete
    - [ ] model not Exists
  - [ ] duplicate rule
  - [ ] cast type
  - [ ] request soft delete
- **Model**
  - [x] relation using pivot class instead table string
  - [x] column name snake_case
  - [ ] split big model
  - [ ] pivot name
  - [ ] test relation foregin key
  - [ ] relation should be index
  - [x] using pivot instead table string
  - [ ] mising cast
- **Migrattion**
  - [x] test can roll back
  - [x] test migrate match db design
  - [ ] test foregin key
- **Cast**
- **Controller**
  - [ ] only public resource method
- **Routes**
  - [x] route kebab-case
  - [x] dont use closure callback
  - [x] use FormRequest instead Request
  - [ ] controller method not found
  - [ ] request match controller
  - [ ] method update should put or patch
  - [ ] remove route empty action
- **resources**
- **resources/lang**
  - [ ] key snake_case
- **resources/assets**
  - [ ] dùng `mix.scripts` thay cho `mix.copy`
- **resources/views**
  - [ ] use `{{ URL::asset() }}` instead `{!! URL::asset() !!}`
- **database**
  - [x] column should not null
  - [x] column should be unsigned
  - [ ] column should not negative
- **storage**:
  - [ ] chmod
- **public**
  - [ ] chmod
- **DotFile**
  - [x] test gitignore
- **Other**
  - [ ] remove all todo
  - [ ] project setup
  - [ ] vimeo/psalm
  - [ ] browserlint
  - [ ] carbon comparerDate
  - [ ] nên dùng laravel Carbon
  - [ ] dont use external url
  - [ ]
  - [ ]
  - [ ]
  - [ ]
  - [ ]
  - [ ] add log delete transaction
  - [ ] document.on insteam element.{event}
  - [ ] custom 404
    - [ ] https://codepen.io/knolcoder/pen/ZEewZaY
    - [ ] https://stackoverflow.com/questions/35774500/how-to-remove-focus-from-a-button-after-botstrap-modal-closed
    - [ ] https://stackoverflow.com/questions/30322918/bootstrap-modal-restores-button-focus-on-close
    - [ ] check css support
    - [ ] tìm hiểu về zerowith character
    - [x] https://github.com/shufo/blade-formatter
    - [ ] Locate request https://github.com/laravel/framework/compare/02fdd82690...503f6e280c
    - [ ] dont allow delete when child relation exist
    - [ ] try cath đúng cách
    - [ ] check format before whereDate
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
    ],
}
```

```php
"repositories": [
    {
        "type": "path",
        "url": "../labs/laravel-audit"
    }
],
```

```shell
composer require socoladaica/laravel-audit:dev-develop --dev
```
