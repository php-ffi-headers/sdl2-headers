<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Headers\SDL2\Tests;

use FFI\Env\Runtime;
use FFI\Headers\SDL2;
use FFI\Headers\SDL2\Platform;
use FFI\Headers\SDL2\Version;
use FFI\Location\Locator;

/**
 * @group binary-compatibility
 * @requires extension ffi
 */
class BinaryCompatibilityTestCase extends TestCase
{
    private const WIN32_ARCHIVE_DIRECTORY = __DIR__ . '/storage/sdl2.win64.zip';
    private const WIN32_BINARY = __DIR__ . '/storage/SDL2.dll';

    public function setUp(): void
    {
        if (!Runtime::isAvailable()) {
            $this->markTestSkipped('An ext-ffi extension must be available and enabled');
        }

        parent::setUp();
    }

    protected function getWindowsBinary(): string
    {
        $version = Version::LATEST->toString();

        // Download glfw archive
        if (!\is_file(self::WIN32_ARCHIVE_DIRECTORY)) {
            $url = \vsprintf('https://www.libsdl.org/release/SDL2-%s-win32-x64.zip', [
                $version
            ]);

            \stream_copy_to_stream(\fopen($url, 'rb'), \fopen(self::WIN32_ARCHIVE_DIRECTORY, 'ab+'));
        }

        if (!\is_file(self::WIN32_BINARY)) {
            $directory = \dirname(self::WIN32_ARCHIVE_DIRECTORY);
            $pathname = $directory . '/SDL2.dll';

            if (!\is_file($pathname)) {
                $phar = new \PharData(self::WIN32_ARCHIVE_DIRECTORY);
                $phar->extractTo($directory, 'SDL2.dll');
            }

            \rename($pathname, self::WIN32_BINARY);
        }

        return self::WIN32_BINARY;
    }

    protected function getLinuxBinary(): string
    {
        $binary = Locator::resolve('libSDL2-2.0.so.0');

        if ($binary === null) {
            $this->markTestSkipped('The [libsdl] library must be installed');
        }

        return (string)$binary;
    }

    protected function getDarwinBinary(): string
    {
        $binary = Locator::resolve('libSDL2-2.0.0.dylib');

        if ($binary === null) {
            $this->markTestSkipped('The [libsdl] library must be installed');
        }

        return (string)$binary;
    }

    protected function assertVersionCompare(Version $version, string $binary): void
    {
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

    /**
     * @requires OSFAMILY Windows
     *
     * @dataProvider versionsDataProvider
     */
    public function testWin32PlatformWithoutContext(Version $version): void
    {
        $this->expectNotToPerformAssertions();

        $binary = $this->getWindowsBinary();

        $this->assertVersionCompare($version, $binary);
        $headers = (string)SDL2::create(Platform::WINDOWS, $version);

        try {
            \FFI::cdef($headers, $binary);
        } catch (\FFI\ParserException $e) {
            $this->dumpExceptionInfo($e, $headers);

            throw $e;
        }
    }

    /**
     * @requires OSFAMILY Linux
     *
     * @dataProvider versionsDataProvider
     */
    public function testLinuxPlatformWithoutContext(Version $version): void
    {
        $this->expectNotToPerformAssertions();

        $binary = $this->getLinuxBinary();

        $this->assertVersionCompare($version, $binary);
        $headers = (string)SDL2::create(Platform::LINUX, $version);

        try {
            \FFI::cdef($headers, $binary);
        } catch (\FFI\ParserException $e) {
            $this->dumpExceptionInfo($e, $headers);

            throw $e;
        }
    }

    /**
     * @requires OSFAMILY Darwin
     *
     * @dataProvider versionsDataProvider
     */
    public function testDarwinPlatformWithoutContext(Version $version): void
    {
        $this->expectNotToPerformAssertions();

        $binary = $this->getLinuxBinary();

        $this->assertVersionCompare($version, $binary);
        $headers = (string)SDL2::create(Platform::DARWIN, $version);

        try {
            \FFI::cdef($headers, $binary);
        } catch (\FFI\ParserException $e) {
            $this->dumpExceptionInfo($e, $headers);

            throw $e;
        }
    }

    /**
     * @dataProvider configDataProvider
     */
    public function testCompilation(Platform $platform, Version $version): void
    {
        $headers = (string)SDL2::create($platform, $version);

        try {
            \FFI::cdef($headers);
            $this->expectNotToPerformAssertions();
        } catch (\FFI\Exception $e) {
            $this->assertStringStartsWith('Failed resolving C function', $e->getMessage());

            if ($e instanceof \FFI\ParserException) {
                $this->dumpExceptionInfo($e, $headers);
            }
        }
    }
}
