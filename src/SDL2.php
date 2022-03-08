<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Headers;

use FFI\Contracts\Headers\HeaderInterface;
use FFI\Contracts\Preprocessor\Exception\DirectiveDefinitionExceptionInterface;
use FFI\Contracts\Preprocessor\Exception\PreprocessorExceptionInterface;
use FFI\Contracts\Preprocessor\PreprocessorInterface;
use FFI\Headers\SDL2\HeadersDownloader;
use FFI\Headers\SDL2\Platform;
use FFI\Headers\SDL2\Version;
use FFI\Headers\SDL2\VersionInterface;
use FFI\Preprocessor\Preprocessor;

class SDL2 implements HeaderInterface
{
    /**
     * @var non-empty-string
     */
    private const HEADERS_DIRECTORY = __DIR__ . '/../resources/headers';

    /**
     * @var non-empty-string
     */
    private const SDLINC_H = <<<'CPP'
    #ifndef SDL_stdinc_h_
        #define SDL_stdinc_h_
        typedef unsigned short wchar_t;

        typedef enum {
            SDL_FALSE = 0,
            SDL_TRUE = 1
        } SDL_bool;

        typedef int8_t Sint8;
        typedef uint8_t Uint8;
        typedef int16_t Sint16;
        typedef uint16_t Uint16;
        typedef int32_t Sint32;
        typedef uint32_t Uint32;
        typedef int64_t Sint64;
        typedef uint64_t Uint64;

        // From WinGW 5.1
        typedef struct _iobuf {
            char*   _ptr;
            int _cnt;
            char*   _base;
            int _flag;
            int _file;
            int _charbuf;
            int _bufsiz;
            char*   _tmpfname;
        } FILE;

        #define SDL_PRINTF_FORMAT_STRING
        #define SDL_SCANF_FORMAT_STRING
        #define SDL_PRINTF_VARARG_FUNC(x)

        #define SDL_FOURCC(A, B, C, D) (A << 0) | (B << 8) | (C << 16) | (D << 24)
        #define SDL_COMPILE_TIME_ASSERT(name, x) typedef int SDL_compile_time_assert_##name_stub
    #endif
    CPP;


    /**
     * @param PreprocessorInterface $pre
     * @param VersionInterface $version
     */
    public function __construct(
        public readonly PreprocessorInterface $pre,
        public readonly VersionInterface $version = Version::LATEST,
    ) {
        if (!$this->exists()) {
            HeadersDownloader::download($this->version, self::HEADERS_DIRECTORY);

            if (!$this->exists()) {
                throw new \RuntimeException('Could not initialize (download) header files');
            }
        }
    }

    /**
     * @return bool
     */
    private function exists(): bool
    {
        return \is_file($this->getHeaderPathname());
    }

    /**
     * @return non-empty-string
     */
    public function getHeaderPathname(): string
    {
        return self::HEADERS_DIRECTORY . '/' . $this->version->toString() . '/SDL.h';
    }

    /**
     * @param Platform|null $platform
     * @param VersionInterface|non-empty-string $version
     * @param PreprocessorInterface $pre
     * @return self
     * @throws DirectiveDefinitionExceptionInterface
     */
    public static function create(
        Platform $platform = null,
        VersionInterface|string $version = Version::LATEST,
        PreprocessorInterface $pre = new Preprocessor(),
    ): self {
        $pre = clone $pre;

        $pre->define('WINAPI_FAMILY_PARTITION', static fn (string $type) => 0);
        $pre->define('DECLSPEC', '');

        // Remove stdinc and platform headers
        $pre->add('SDL_stdinc.h', self::SDLINC_H);

        switch ($platform) {
            case Platform::WINDOWS:
                $pre->define('__WIN32__', '1');
                $pre->define('__stdcall');
                $pre->define('__cdecl');
                $pre->add('process.h', '');
                break;

            case Platform::LINUX:
                $pre->define('__LINUX__', '1');
                break;

            case Platform::DARWIN:
                $pre->define('__APPLE__', '1');
                $pre->add('AvailabilityMacros.h', '');
                $pre->add('TargetConditionals.h', '');
                $pre->add('signal.h', '');
                break;

            case Platform::FREEBSD:
                $pre->define('__FREEBSD__', '1');
                break;
        }

        if (!$version instanceof VersionInterface) {
            $version = Version::create($version);
        }

        return new self($pre, $version);
    }

    /**
     * @return non-empty-string
     * @throws PreprocessorExceptionInterface
     */
    public function __toString(): string
    {
        $result = $this->pre->process(new \SplFileInfo($this->getHeaderPathname())) . \PHP_EOL;

        $result = $this->withoutMainFunction($result);
        $result = $this->withoutStaticInline($result);

        return $result;
    }

    /**
     * @param string $result
     * @return string
     */
    private function withoutMainFunction(string $result): string
    {
        $from = [
            'extern  int SDL_main(int argc, char *argv[]);',
            'extern   int SDL_main(int argc, char *argv[]);',
        ];

        return \str_replace($from, '', $result);
    }

    /**
     * @param string $result
     * @return string
     */
    private function withoutStaticInline(string $result): string
    {
        while (($offset = \strpos($result, 'static inline')) !== false) {
            $to = $from = $offset;
            $depth = 0;

            do {
                switch ($result[$to]) {
                    case ';':
                        if ($depth === 0) {
                            $result = \substr($result, 0, $from)
                                . \substr($result, $to + 1);
                            continue 3;
                        }
                        break;

                    case '{':
                        $depth++;
                        break;

                    case '}':
                        $depth--;
                        if ($depth <= 0) {
                            while ($result[$to + 1] === ';') {
                                $to++;
                            }

                            $result = \substr($result, 0, $from)
                                . \substr($result, $to + 1);
                            continue 3;
                        }
                        break;
                }
            } while (isset($result[$to++]));
        }

        return $result;
    }
}
