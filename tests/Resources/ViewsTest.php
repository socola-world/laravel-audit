<?php

namespace SocolaDaiCa\LaravelAudit\Tests\Resources;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SocolaDaiCa\LaravelAudit\Tests\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class ViewsTest extends TestCase
{
    public function test_views()
    {
        $dir = resource_path('views');

        if (File::ensureDirectoryExists($dir) === false) {
            $this->assertTrue(true);
            return;
        }

        /**
         * @var SplFileInfo[] $files
         */
        $files = File::allFiles($dir);

        $this->assertTrue(true);
        $this->follow_test_relative_pathname($files);
        $this->follow_test_bracket($files);
    }

    /**
     * @param SplFileInfo[] $files
     * @return void
     */
    public function follow_test_relative_pathname($files)
    {
        $bladeWrongPaths = collect($files)
            ->map(fn(SplFileInfo $file) => $file->getRelativePathname())
            ->filter(fn(string $path) => preg_match('/^[a-z0-9\-\/\\\.]+$/', $path) == false)
            ->values()
            ->toArray()
        ;

//        $this->shouldWarning(fn() => $this->assertEmpty(
//            $bladeWrongPaths,
//            $this->warning(
//                'blade path must is kebab-case',
//                $bladeWrongPaths
//            )
//        ));
    }

    /**
     * @param SplFileInfo[] $files
     * @return void
     */
    public function follow_test_bracket($files)
    {
        foreach ($files as $file) {
            $content = file_get_contents($file->getPathname());
            $this->shouldWarning(function () use (&$file, &$content) {
                /* {{ */
                $this->assertEquals(0, preg_match_all(
                    '/\{\{(?!--)(?:.*[^\s]|[^\s].*)(?<!--)}}/', $content, $maches),
                    $this->warning(
                        $file->getPathname(),
                        'missing space between {{ $variable }}',
                        $maches[0],
                        'use "\{\{\s*(.*)\s*!!}" => "{{ $1 }}" for fast replace'
                    )
                );
            });

            $this->shouldWarning(function () use (&$file, &$content) {
                $this->assertEquals(0, preg_match_all(
                    '/\{\{(?!--)(?:.*\s{2,}|\s{2,}.*)(?<!--)}}/', $content, $maches),
                    $this->warning(
                        $file->getPathname(),
                        'too many space between {{ $variable }}',
                        $maches[0],
                        'use "\{\{\s*(.*)\s*!!}" => "{{ $1 }}" for fast replace'
                    )
                );
            });

            $this->shouldWarning(function () use (&$file, &$content) {
                /* {!! */
                $this->assertEquals(0, preg_match_all(
                    '/\{!!(?:.*[^\s]|[^\s].*)!!}/', $content, $maches),
                    $this->warning(
                        $file->getPathname(),
                        'missing space between {!! $variable !!}',
                        $maches[0],
                        'use "\{!!\s*(.*)\s*!!}" => "{!! $1 !!}" for fast replace'
                    )
                );
            });
            $this->shouldWarning(function () use (&$file, &$content) {
                $this->assertEquals(0, preg_match_all(
                    '/\{!!(?:.*\s]{2,}|\s{2,}.*)!!}/', $content, $maches),
                    $this->warning(
                        $file->getPathname(),
                        'too many space between {!! $variable !!}',
                        $maches[0],
                        'use "\{!!\s*(.*)\s*!!}" => "{!! $1 !!}" for fast replace'
                    )
                );
            });
            $this->shouldWarning(function () use (&$file, &$content) {
                $this->assertEquals(0, preg_match_all(
                    '/\{!!\s(?:action|asset|route|secure_asset|url)\((?:.*)\)\s!!}/', $content, $maches),
                    $this->warning(
                        $file->getPathname(),
                        'should use {{ $variable }} instead {!! $variable !!}',
                        $maches[0],
                        'use "\{!! (.*) !!}" => "{{ $1 }}" for fast replace'
                    )
                );
            });
            $this->assertEquals(0, preg_match_all(
                '/(?:\{\{\s|\{\{\s|@)(?:dd|dump)\(.*\)(?:}}|!!}|)/', $content, $maches),
                $this->error(
                    $file->getPathname(),
                    'remove dd or dump in blade',
                    $maches[0],
                )
            );
        }
    }
}
