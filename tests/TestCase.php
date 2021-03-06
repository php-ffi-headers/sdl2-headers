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
use FFI\Headers\Testing\TestingTrait;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use TestingTrait;

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
     * @return array<array{Version}>
     */
    public function versionsDataProvider(): array
    {
        $result = [];

        foreach (Version::cases() as $version) {
            $result[$version->toString()] = [$version];
        }

        return $result;
    }
}
