<p align="center">
    <a href="https://github.com/ffi-libs">
        <img src="https://avatars.githubusercontent.com/u/101121010?s=256" width="128" />
    </a>
</p>

<p align="center">
    <a href="https://github.com/php-ffi-libs/sdl2-headers/actions"><img src="https://github.com/php-ffi-libs/sdl2-headers/workflows/build/badge.svg"></a>
    <a href="https://packagist.org/packages/ffi-libs/sdl2-headers"><img src="https://img.shields.io/badge/PHP-8.1.0-ff0140.svg"></a>
    <a href="https://packagist.org/packages/ffi-libs/sdl2-headers"><img src="https://img.shields.io/badge/SDL2-2.0.20-cc3c20.svg"></a>
    <a href="https://packagist.org/packages/ffi-libs/sdl2-headers"><img src="https://poser.pugx.org/ffi-libs/sdl2-headers/version" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/ffi-libs/sdl2-headers"><img src="https://poser.pugx.org/ffi-libs/sdl2-headers/v/unstable" alt="Latest Unstable Version"></a>
    <a href="https://packagist.org/packages/ffi-libs/sdl2-headers"><img src="https://poser.pugx.org/ffi-libs/sdl2-headers/downloads" alt="Total Downloads"></a>
    <a href="https://raw.githubusercontent.com/php-ffi-libs/sdl2-headers/master/LICENSE.md"><img src="https://poser.pugx.org/ffi-libs/sdl2-headers/license" alt="License MIT"></a>
</p>

# SDL2 Headers

This is a C headers of the [SDL2](https://www.libsdl.org/download-2.0.php) adopted for PHP.

## Requirements

- PHP >= 8.1

## Installation

Library is available as composer repository and can be installed using the
following command in a root of your project.

```sh
$ composer require ffi-libs/sdl2-headers
```

## Usage

```php
use FFI\Headers\SDL2;

$headers = SDL2::create(
    SDL2\Version::V2_0_20, // SDL2 Headers Version
);

echo $headers;
```

> Please note that the use of header files is not the latest version:
> - Takes time to download and install (This will be done in the background
    >   during initialization).
> - May not be compatible with the PHP headers library.

