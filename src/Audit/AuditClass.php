<?php

namespace SocolaDaiCa\LaravelAudit\Audit;

use Doctrine\Common\Annotations\PhpParser;
use ReflectionClass;
use SplFileObject;

class AuditClass extends Audit1
{
    /**
     * @var ReflectionClass
     */
    public $reflectionClass;

    /**
     * @var PhpParser
     */
    protected static $phpParser;

    private static $cache = [];

    public static function make(ReflectionClass $reflectionClass)
    {
        if (array_key_exists($reflectionClass->getName(), static::$cache)) {
            return static::$cache[$reflectionClass->getName()];
        }

        return static::$cache[$reflectionClass->getName()] = new static($reflectionClass);
    }

    public static function makeByClass($class)
    {
        if (array_key_exists($class, static::$cache)) {
            return static::$cache[$class];
        }

        return static::$cache[$class] = new static(new ReflectionClass($class));
    }

    public function __construct(ReflectionClass $reflectionClass)
    {
        $this->reflectionClass = $reflectionClass;
    }

    public function hasTrait($trait)
    {
        return in_array($trait, $this->reflectionClass->getTraitNames());
    }

    /**
     * @return PhpParser
     */
    public static function getPhpParser(): PhpParser
    {
        if (!static::$phpParser) {
            self::$phpParser = new PhpParser();
        }

        return self::$phpParser;
    }

    /**
     * @return array
     */
    public function getUseStatements(): array
    {
        return once(function () {
            return self::getPhpParser()->parseUseStatements($this->reflectionClass);
        });
    }

    /**
     * Gets the content of the file right up to the given line number.
     *
     * @param string $filename The name of the file to load.
     * @param int $lineNumber The number of lines to read from file.
     *
     * @return string|null The content of the file or null if the file does not exist.
     */
    private function getFileContent($filename, $lineNumber)
    {
        if (!is_file($filename)) {
            return null;
        }

        $content = '';
        $lineCnt = 0;
        $file = new SplFileObject($filename);
        while (!$file->eof()) {
            if ($lineCnt++ === $lineNumber) {
                break;
            }

            $content .= $file->fgets();
        }

        return $content;
    }

    /**
     * Parse a class or function for use statements.
     *
     * @psalm-return array<string, string> a list with use statements in the form (Alias => FQN).
     */
    public function getUseFuntions(): array
    {
        $filename = $this->reflectionClass->getFileName();

        if ($filename === false) {
            return [];
        }

        $content = $this->getFileContent($filename, $this->reflectionClass->getStartLine());

        if ($content === null) {
            return [];
        }

        $namespace = preg_quote($this->reflectionClass->getNamespaceName());
        $content = preg_replace('/^.*?(\bnamespace\s+'.$namespace.'\s*[;{].*)$/s', '\\1', $content);
        $tokenizer = new \SocolaDaiCa\LaravelAudit\Audit\TokenParser('<?php '.$content);

        return $tokenizer->parseUseFuntions($this->reflectionClass->getNamespaceName());
    }
}
