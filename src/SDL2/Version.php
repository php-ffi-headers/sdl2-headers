<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Headers\SDL2;

use FFI\Contracts\Headers\Version as CustomVersion;
use FFI\Contracts\Headers\Version\Comparable;
use FFI\Contracts\Headers\Version\ComparableInterface;
use FFI\Contracts\Headers\VersionInterface;

enum Version: string implements ComparableInterface
{
    use Comparable;

    case V2_0_0 = '2.0.0';
    case V2_0_1 = '2.0.1';
    case V2_0_2 = '2.0.2';
    case V2_0_3 = '2.0.3';
    case V2_0_4 = '2.0.4';
    case V2_0_5 = '2.0.5';
    case V2_0_6 = '2.0.6';
    case V2_0_7 = '2.0.7';
    case V2_0_8 = '2.0.8';
    case V2_0_9 = '2.0.9';
    case V2_0_10 = '2.0.10';
    case V2_0_12 = '2.0.12';
    case V2_0_14 = '2.0.14';
    case V2_0_16 = '2.0.16';
    case V2_0_18 = '2.0.18';
    case V2_0_20 = '2.0.20';
    case V2_0_22 = '2.0.22';

    public const LATEST = self::V2_0_22;

    /**
     * @param non-empty-string $version
     * @return VersionInterface
     */
    public static function create(string $version): VersionInterface
    {
        /** @var array<non-empty-string, VersionInterface> $versions */
        static $versions = [];

        return self::tryFrom($version)
            ?? $versions[$version]
            ??= CustomVersion::fromString($version);
    }

    /**
     * {@inheritDoc}
     */
    public function toString(): string
    {
        return $this->value;
    }
}
