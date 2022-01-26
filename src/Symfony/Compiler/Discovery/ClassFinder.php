<?php
declare(strict_types=1);

namespace Zalas\Injector\PHPUnit\Symfony\Compiler\Discovery;

class ClassFinder
{
    private string $path;

    public function __construct(string $path = 'tests')
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
            return \in_array($interface, \class_implements($fqcn));
        });
    }

    /**
     * @param callable $predicate
     * @return string[]
     */
    private function find(callable $predicate): array
    {
        $classes = [];

        foreach ($this->findPhpFiles() as $phpFile) {
            $classes[] = $this->findClassInFile($phpFile);
        }

        $classes = \array_filter($classes, function (?string $class) use ($predicate) {
            return !empty($class) && $predicate($class);
        });

        \sort($classes);

        return $classes;
    }

    private function findClassInFile(\SplFileInfo $phpFile): ?string
    {
        // @see https://stackoverflow.com/a/27440555/330267
        $tokens = \token_get_all(\file_get_contents($phpFile->getRealPath()));
        $namespace = '';

        for ($index = 0; isset($tokens[$index]); $index++) {
            if (!\is_array($tokens[$index])) {
                continue;
            }
            if ($this->isNamespaceToken($tokens, $index)) {
                while ($this->isNotWhitespaceToken($tokens, $index)) {
                    $namespace .= $tokens[$index++][1];
                }
            }
            if ($this->isClassNameToken($tokens, $index)) {
                return $namespace . '\\' . $tokens[$index][1];
            }
        }

        return null;
    }

    private function isNamespaceToken($tokens, int $index): bool
    {
        return $this->extractTokens($tokens, $index - 2, 3) === [T_NAMESPACE, T_WHITESPACE, T_NAME_QUALIFIED];
    }

    private function isClassNameToken($tokens, int $index): bool
    {
        return $this->extractTokens($tokens, $index - 2, 3) === [T_CLASS, T_WHITESPACE, T_STRING];
    }

    private function extractTokens($tokens, int $startIndex, int $count): array
    {
        return \array_column(\array_slice($tokens, $startIndex, $count), 0);
    }

    private function isNotWhitespaceToken($tokens, int $index): bool
    {
        return isset($tokens[$index]) && \is_array($tokens[$index]) && T_WHITESPACE !== $tokens[$index][0];
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
