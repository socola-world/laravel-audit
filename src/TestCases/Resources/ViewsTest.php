<?php

namespace SocolaDaiCa\LaravelAudit\TestCases\Resources;

use Illuminate\Support\Str;
use SocolaDaiCa\LaravelAudit\Audit\AuditView;
use SocolaDaiCa\LaravelAudit\TestCases\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class ViewsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testRelativePathname()
    {
        $bladeWrongPaths = $this->views()
            ->map(fn (SplFileInfo $file) => $file->getRelativePathname())
            ->filter(fn (string $path) => preg_match('/^[a-z0-9\-\/\\\.]+$/', $path) == false)
            ->map(fn (string $path) => Str::replace('\\', '/', $path))
            ->values()
            ->toArray()
        ;

        static::assertEmpty(
            $bladeWrongPaths,
            $this->error(
                'View path should is kebab case',
                $bladeWrongPaths,
            ),
        );
    }

    public function testBracketSpace()
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
                        $maches,
                    ),
                    $this->warning(
                        $file->getPathname(),
                        'missing space between {{ $variable }}',
                        $maches[0],
                        'use "(\{\{(?!--)([^\s].*[^\s])(?<!--)}}|\{\{(?!--)([^\s].*[^\s])\s(?<!--)}}|\{\{(?!--)\s([^\s].*[^\s])(?<!--)}}|\{\{(?!--)\s{2,}([^\s].*[^\s])(?<!--)}}|\{\{(?!--)\s{2,}([^\s].*[^\s])\s{2,}(?<!--)}})" => "{{ $2$3$3$4$5$6 }}" for fast replace',
                    ),
                );
            });

            $this->shouldWarning(function () use (&$file, &$content) {
                $this->assertEquals(
                    0,
                    preg_match_all(
                        '/\{\{(?!--)(?:.*\s{2,}|\s{2,}.*)(?<!--)}}/',
                        $content,
                        $maches,
                    ),
                    $this->warning(
                        $file->getPathname(),
                        'too many space between {{ $variable }}',
                        $maches[0],
                        'use "(\{\{(?!--)([^\s].*[^\s])(?<!--)}}|\{\{(?!--)([^\s].*[^\s])\s(?<!--)}}|\{\{(?!--)\s([^\s].*[^\s])(?<!--)}}|\{\{(?!--)\s{2,}([^\s].*[^\s])(?<!--)}}|\{\{(?!--)\s{2,}([^\s].*[^\s])\s{2,}(?<!--)}})" => "{{ $2$3$3$4$5$6 }}" for fast replace',
                    ),
                );
            });

            $this->shouldWarning(function () use (&$file, &$content) {
                /* {!! */
                $this->assertEquals(
                    0,
                    preg_match_all(
                        '/\{!!(?:.*[^\s]|[^\s].*)!!}/',
                        $content,
                        $maches,
                    ),
                    $this->warning(
                        $file->getPathname(),
                        'missing space between {!! $variable !!}',
                        $maches[0],
                        'use "(\{!!(?!--)([^\s].*[^\s])(?<!--)!!}|\{!!(?!--)([^\s].*[^\s])\s(?<!--)!!}|\{!!(?!--)\s([^\s].*[^\s])(?<!--)!!}|\{!!(?!--)\s{2,}([^\s].*[^\s])(?<!--)!!}|\{!!(?!--)\s{2,}([^\s].*[^\s])\s{2,}(?<!--)!!})" => "{!! $2$3$3$4$5$6 !!}" for fast replace',
                    ),
                );
            });

            $this->shouldWarning(function () use (&$file, &$content) {
                $this->assertEquals(
                    0,
                    preg_match_all(
                        '/\{!!(?:.*\s]{2,}|\s{2,}.*)!!}/',
                        $content,
                        $maches,
                    ),
                    $this->warning(
                        $file->getPathname(),
                        'too many space between {!! $variable !!}',
                        $maches[0],
                        'use "(\{!!(?!--)([^\s].*[^\s])(?<!--)!!}|\{!!(?!--)([^\s].*[^\s])\s(?<!--)!!}|\{!!(?!--)\s([^\s].*[^\s])(?<!--)!!}|\{!!(?!--)\s{2,}([^\s].*[^\s])(?<!--)!!}|\{!!(?!--)\s{2,}([^\s].*[^\s])\s{2,}(?<!--)!!})" => "{!! $2$3$3$4$5$6 !!}" for fast replace',
                    ),
                );
            });

            $this->shouldWarning(function () use (&$file, &$content) {
                $this->assertEquals(
                    0,
                    preg_match_all(
                        '/\{!!\s*(?:action|asset|route|secure_asset|url|mix)\((?:.*)\)\s*!!}/',
                        $content,
                        $maches,
                    ),
                    $this->warning(
                        $file->getPathname(),
                        'should use {{ $variable }} instead {!! $variable !!}',
                        $maches[0],
                        'use "\{!! (.*) !!}" => "{{ $1 }}" for fast replace',
                    ),
                );
            });

            static::assertEquals(
                0,
                preg_match_all(
                    '/(?:\{\{\s|\{\{\s|@)(?:dd|dump)\(.*\)(?:}}|!!}|)/',
                    $content,
                    $maches,
                ),
                $this->error(
                    $file->getPathname(),
                    'remove dd or dump in blade',
                    $maches[0],
                ),
            );
        }
    }

    protected static ?array $mixManifestAssets = null;

    public function mixManifestAssets()
    {
        if (static::$mixManifestAssets !== null) {
            return static::$mixManifestAssets;
        }

        $this->addWarning('Make sure you run npm run dev before run this testcase');
        $mixManifestPath = public_path('mix-manifest.json');

        if (!file_exists($mixManifestPath)) {
            return static::$mixManifestAssets = [];
        }

        $assets = file_get_contents($mixManifestPath);
        $assets = json_decode($assets);
        $assets = (array) $assets;
        $assets = array_keys($assets);
        $assets = array_map(fn ($file) => trim($file, '/'), $assets);

        return static::$mixManifestAssets = $assets;
    }

    /**
     * @dataProvider viewDataProvider
     */
    public function testAssetMix(AuditView $auditView)
    {
        $content = $auditView->getContent();
        $assetDirString = implode('|', config('socoladaica__laravel_audit.asset_dir'));

        $assetDirString = implode('|', $this->mixManifestAssets());

        $assetDirString = str_replace('/', '\/', $assetDirString);

        preg_match_all('/(?:\{\{|\{!!)\s*(?:secure_asset|asset|url)\(["\']\/?(?:'.$assetDirString.').*(?:}}|!!})/', $content, $matches);
        $matches = $matches[0];

        $matchNews = [];

        foreach ($matches as $match) {
            $matchNew = $match;

            $matchNew = preg_replace(
                '/^(\{\{|\{!!)\s*(secure_asset|asset|url)\((["\']\/?)('.$assetDirString.')(.*)\s+(}}|!!})$/',
                '$1 $2(mix($3$4$5) $6',
                $matchNew
            );

            $matchNews[$match] = $matchNew;
        }

        static::assertEmpty(
            $matches,
            $this->error(
                $auditView->fileInfo->getPathname(),
                'missing mix',
                $matchNews,
            )
        );
    }
}
