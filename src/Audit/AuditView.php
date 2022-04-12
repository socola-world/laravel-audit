<?php

namespace SocolaDaiCa\LaravelAudit\Audit;

use Symfony\Component\Finder\SplFileInfo;

class AuditView
{
    public $fileInfo;

    protected $content;

    public function __construct(SplFileInfo $fileInfo)
    {
        $this->fileInfo = $fileInfo;
    }

    public static function make(SplFileInfo $fileInfo)
    {
        return new static($fileInfo);
    }

    public function getContent(): string
    {
        if ($this->content !== null) {
            return $this->content;
        }

        return $this->content = file_get_contents($this->fileInfo->getPathname());
    }
}
