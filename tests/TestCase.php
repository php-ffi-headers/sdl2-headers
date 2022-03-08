<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Headers\SDL2\Tests;

use FFI\Headers\SDL2\Version;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @return array<array{Version}>
     */
    public function configDataProvider(): array
    {
        $result = [];

        foreach (Version::cases() as $version) {
            $result[\sprintf('%s', $version->value)] = [$version];
        }

        return $result;
    }
}
