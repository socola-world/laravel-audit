<?php

namespace SocolaDaiCa\LaravelAudit\TestCases;

use Dotenv\Dotenv;
use function base_path;

class EnvTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (file_exists(base_path('.env.example')) === false) {
            static::markTestSkipped('.env.example not found');
        }

        if (file_exists(base_path('')) === false) {
            static::markTestSkipped('.env not found');
        }
    }

    /**
     * @throws \JsonException
     */
    public function testKeyNotDefineInEnvExample()
    {
        /**
         * @var \Dotenv\Dotenv $dotenvExample
         */
        $dotenvExample = Dotenv::createImmutable(base_path(''), '.env.example');

        /**
         * @var \Dotenv\Dotenv $dotenv
         */
        $dotenv = Dotenv::createImmutable(base_path(''), '.env');

        $keyNotDefineInEnvExample = array_diff_key($dotenv->load(), $dotenvExample->load());

        static::assertEmpty(
            $keyNotDefineInEnvExample,
            $this->error(
                '.env.example missing keys',
                $keyNotDefineInEnvExample
            )
        );
    }
}
