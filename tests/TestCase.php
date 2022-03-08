<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Headers\SDL2\Tests;

use FFI\Headers\SDL2\Platform;
use FFI\Headers\SDL2\Version;
use FFI\ParserException;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @return array<array{Platform, Version}>
     */
    public function configDataProvider(): array
    {
        $result = [];

        foreach (Platform::cases() as $platform) {
            foreach (Version::cases() as $version) {
                $result[\sprintf('%s-%s', $platform->name, $version->value)] = [$platform, $version];
            }
        }

        return $result;
    }

    /**
     * @param ParserException $e
     * @param string $header
     * @return void
     */
    protected function dumpExceptionInfo(ParserException $e, string $header): void
    {
        \preg_match('/at line (\d+)/isum', $e->getMessage(), $matches);

        $size = 2;
        $line = (int)($matches[1] ?? 1) - 1;

        $lines = \explode("\n", $header);
        echo "\n";
        for ($i = \max(0, $line) - $size, $to = $line + $size; $i <= $to; ++$i) {
            if ($line === $i) {
                echo \sprintf("%5d. | \u{001b}[41m\u{001b}[37;1m%s\u{001b}[0m", $i + 1, $lines[$i] ?? '') . "\n";
            } else {
                echo \sprintf('%5d. | %s', $i + 1, $lines[$i] ?? '') . "\n";
            }
        }
    }
}
