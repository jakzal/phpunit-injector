<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\TestCase;

class ClassFinder
{
    /**
     * @var string
     */
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @param string $interface
     * @return string[]
     */
    public function findImplementations(string $interface): array
    {
        return $this->find(function (string $fqcn) use ($interface) {
            return in_array($interface, class_implements($fqcn));
        });
    }

    /**
     * @param callable $predicate
     * @return string[]
     */
    private function find(callable $predicate): array
    {
        $classes = array();

        foreach ($this->findPhpFiles() as $phpFile) {
            $classes[] = $this->findClassesInFile($predicate, $phpFile);
        }

        $classes = array_merge([], ...$classes);

        sort($classes);

        return $classes;
    }

    private function findClassesInFile(callable $predicate, \SplFileInfo $phpFile): array
    {
        // @see https://stackoverflow.com/a/27440555/330267
        $classes = [];
        $tokens = token_get_all(file_get_contents($phpFile->getRealPath()));
        $namespace = '';

        for ($index = 0; isset($tokens[$index]); $index++) {
            if (!isset($tokens[$index][0])) {
                continue;
            }
            if (T_NAMESPACE === $tokens[$index][0]) {
                $index += 2; // Skip namespace keyword and whitespace
                while (isset($tokens[$index]) && is_array($tokens[$index])) {
                    $namespace .= $tokens[$index++][1];
                }
            }
            if (T_CLASS === $tokens[$index][0]) {
                $index += 2; // Skip class keyword and whitespace
                $class = $namespace . '\\' . $tokens[$index][1];
                if ($predicate($class)) {
                    $classes[] = $class;
                }
            }
        }
        return $classes;
    }

    /**
     * @return \SplFileInfo[]|\Iterator
     */
    private function findPhpFiles(): \Iterator
    {
        return new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->path)
            ),
            '/\.php$/'
        );
    }
}