<?php

namespace SocolaDaiCa\LaravelAudit\Tests\Resources;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SocolaDaiCa\LaravelAudit\Tests\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class ViewsTest extends TestCase
{
//    public function test_views()
//    {
//        $dir = resource_path('views');
//
//        /**
//         * @var SplFileInfo[] $files
//         */
//        $files = File::allFiles($dir);
//
////        $this->follow_test_relative_pathname($files);
//        $this->follow_test_bracket($files);
//    }

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

        $this->assertEmpty(
            $bladeWrongPaths,
            $this->echo(
                'blade path must is kebab-case',
                $bladeWrongPaths
            )
        );
    }

    /**
     * @param SplFileInfo[] $files
     * @return void
     */
    public function follow_test_bracket($files)
    {
        foreach ($files as $file) {
            dd($file, $file->getPathname());
            $content = file_get_contents($file->getPath());
            dd($file);
        }
//        $bladeWrongPaths = collect($files)
//            ->map(fn(SplFileInfo $file) => $file->getRelativePathname())
//            ->filter(fn(string $path) => preg_match('/^[a-z0-9\-\/\\\.]+$/', $path) == false)
//            ->values()
//            ->toArray()
//        ;
//
//        $this->assertEmpty(
//            $bladeWrongPaths,
//            $this->echo(
//                'blade path must is kebab-case',
//                $bladeWrongPaths
//            )
//        );
    }
}
