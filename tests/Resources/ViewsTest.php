<?php

namespace SocolaDaiCa\LaravelAudit\Tests\Resources;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SocolaDaiCa\LaravelAudit\Tests\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class ViewsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @return Collection|SplFileInfo[]
     */
    public function views(): Collection
    {
        return once(function () {
            $dir = resource_path('views');

            if (is_dir($dir) == false) {
                return [];
            }

            /**
             * @var SplFileInfo[] $files
             */
            $files = File::allFiles($dir);

            return collect($files);
        });
    }

    public function testRelativePathname()
    {
        $bladeWrongPaths = $this->views()
            ->map(fn (SplFileInfo $file) => $file->getRelativePathname())
            ->filter(fn (string $path) => preg_match('/^[a-z0-9\-\/\\\.]+$/', $path) == false)
            ->map(fn (string $path) => Str::replace('\\', '/', $path))
            ->values()
            ->toArray();

        static::assertEmpty(
            $bladeWrongPaths,
            $this->error(
                'View path should is kebab case',
                $bladeWrongPaths
            )
        );
    }

    public function testBracket()
    {
        $files = $this->views();

        foreach ($files as $file) {
            $content = file_get_contents($file->getPathname());
            $this->shouldWarning(function () use (&$file, &$content) {
                /* {{ */
                $this->assertEquals(
                    0,
                    preg_match_all(
                        '/\{\{(?!--)(?:.*[^\s]|[^\s].*)(?<!--)}}/',
                        $content,
                        $maches
                    ),
                    $this->warning(
                        $file->getPathname(),
                        'missing space between {{ $variable }}',
                        $maches[0],
                        'use "\{\{(?!--)(?:\s*)([^\s].*[^\s])(?:\s*)(?<!--)}}" => "{{ $1 }}" for fast replace'
                    )
                );
            });

            $this->shouldWarning(function () use (&$file, &$content) {
                $this->assertEquals(
                    0,
                    preg_match_all(
                        '/\{\{(?!--)(?:.*\s{2,}|\s{2,}.*)(?<!--)}}/',
                        $content,
                        $maches
                    ),
                    $this->warning(
                        $file->getPathname(),
                        'too many space between {{ $variable }}',
                        $maches[0],
                        'use "\{\{(?!--)(?:\s*)([^\s].*[^\s])(?:\s*)(?<!--)}}" => "{{ $1 }}" for fast replace'
                    )
                );
            });

            $this->shouldWarning(function () use (&$file, &$content) {
                /* {!! */
                $this->assertEquals(
                    0,
                    preg_match_all(
                        '/\{!!(?:.*[^\s]|[^\s].*)!!}/',
                        $content,
                        $maches
                    ),
                    $this->warning(
                        $file->getPathname(),
                        'missing space between {!! $variable !!}',
                        $maches[0],
                        'use "\{!!\s*(.*)\s*!!}" => "{!! $1 !!}" for fast replace'
                    )
                );
            });

            $this->shouldWarning(function () use (&$file, &$content) {
                $this->assertEquals(
                    0,
                    preg_match_all(
                        '/\{!!(?:.*\s]{2,}|\s{2,}.*)!!}/',
                        $content,
                        $maches
                    ),
                    $this->warning(
                        $file->getPathname(),
                        'too many space between {!! $variable !!}',
                        $maches[0],
                        'use "\{!!\s*(.*)\s*!!}" => "{!! $1 !!}" for fast replace'
                    )
                );
            });

            $this->shouldWarning(function () use (&$file, &$content) {
                $this->assertEquals(
                    0,
                    preg_match_all(
                        '/\{!!\s(?:action|asset|route|secure_asset|url)\((?:.*)\)\s!!}/',
                        $content,
                        $maches
                    ),
                    $this->warning(
                        $file->getPathname(),
                        'should use {{ $variable }} instead {!! $variable !!}',
                        $maches[0],
                        'use "\{!! (.*) !!}" => "{{ $1 }}" for fast replace'
                    )
                );
            });

            static::assertEquals(
                0,
                preg_match_all(
                    '/(?:\{\{\s|\{\{\s|@)(?:dd|dump)\(.*\)(?:}}|!!}|)/',
                    $content,
                    $maches
                ),
                $this->error(
                    $file->getPathname(),
                    'remove dd or dump in blade',
                    $maches[0],
                )
            );
        }
    }
}
