<?php

namespace SocolaDaiCa\LaravelAudit\TestCases;

class DotFileTest extends TestCase
{
    public function testGitignore()
    {
        $files = [
            'public/mix-manifest.json',
            '.env',
            'storage/*.key',
            '.phpunit.result.cache',
            '_ide_helper.php',
            '_ide_helper_models.php',
            '.phpstorm.meta.php',
            '.php-cs-fixer.cache',
        ];

        $folders = [
            'node_modules',
            'vendor',
            '.idea',
            'public/storage',
            'public/css',
            'public/js',
        ];

        $gitignoreLines = [];
        if (file_exists(base_path('.gitignore'))) {
            $gitignoreLines = file_get_contents(base_path('.gitignore'));
            $gitignoreLines = explode("\r\n", $gitignoreLines);
        }

        $gitignoreLines = collect($gitignoreLines)
            ->map(fn ($e) => trim($e, '\\/'))
            ->values()
            ->toArray()
        ;

        $gitignoreLinesMissing = [];
        foreach (array_merge($files, $folders) as $item) {
            if (in_array($item, $gitignoreLines)) {
                continue;
            }

            if (is_dir(base_path($item)) || glob(base_path($item))) {
                $gitignoreLinesMissing[] = $item;
            }
        }

        $this->assertEmpty(
            $gitignoreLinesMissing,
            $this->error(
                ".gitignore missing",
                "\n".implode("\n", $gitignoreLinesMissing)
            )
        );
    }
}
