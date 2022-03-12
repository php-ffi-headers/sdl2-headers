<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Headers\SDL2\Tests;

use FFI\Headers\SDL2;
use FFI\Headers\SDL2\Platform;
use FFI\Headers\SDL2\Version;
use FFI\Headers\Testing\Downloader;
use FFI\Location\Locator;

class BinaryCompatibilityTestCase extends TestCase
{
    protected function skipIfVersionNotCompatible(Version $version, string $binary): void
    {
        $this->skipIfNoFFISupport();

        $ffi = \FFI::cdef(<<<'CPP'
        typedef struct SDL_version {
            uint8_t major;
            uint8_t minor;
            uint8_t patch;
        } SDL_version;

        extern void SDL_GetVersion(SDL_version *ver);
        CPP, $binary);

        $ver = $ffi->new('SDL_version');
        $ffi->SDL_GetVersion(\FFI::addr($ver));
        $actual = \sprintf('%d.%d.%d', $ver->major, $ver->minor, $ver->patch);

        if (\version_compare($version->toString(), $actual, '>')) {
            $message = 'Unable to check compatibility because the installed version of the '
                . 'library (v%s) is lower than the tested headers (v%s)';

            $this->markTestSkipped(\sprintf($message, $actual, $version->toString()));
        }
    }

    /**
     * @requires OSFAMILY Windows
     * @dataProvider versionsDataProvider
     */
    public function testWin32PlatformWithoutContext(Version $version): void
    {
        if (!\is_file($binary = __DIR__ . '/storage/SDL2.dll')) {
            Downloader::zip('https://www.libsdl.org/release/SDL2-%s-win32-x64.zip', [
                Version::LATEST->toString(),
            ])
                ->extract('SDL2.dll', $binary);
        }

        $this->skipIfVersionNotCompatible($version, $binary);
        $this->assertHeadersCompatibleWith(SDL2::create(Platform::WINDOWS, $version), $binary);
    }

    /**
     * @requires OSFAMILY Linux
     * @dataProvider versionsDataProvider
     */
    public function testLinuxPlatformWithoutContext(Version $version): void
    {
        if (($binary = Locator::resolve('libSDL2-2.0.so.0')) === null) {
            $this->markTestSkipped('The [libsdl] library must be installed');
        }

        $this->skipIfVersionNotCompatible($version, $binary);
        $this->assertHeadersCompatibleWith(SDL2::create(Platform::LINUX, $version), $binary);
    }

    /**
     * @requires OSFAMILY Darwin
     * @dataProvider versionsDataProvider
     */
    public function testDarwinPlatformWithoutContext(Version $version): void
    {
        if (($binary = Locator::resolve('libSDL2-2.0.0.dylib')) === null) {
            $this->markTestSkipped('The [libsdl] library must be installed');
        }

        $this->skipIfVersionNotCompatible($version, $binary);
        $this->assertHeadersCompatibleWith(SDL2::create(Platform::DARWIN, $version), $binary);
    }
}
