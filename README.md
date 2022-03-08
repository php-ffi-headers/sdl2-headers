<p align="center">
    <a href="https://github.com/ffi-libs">
        <img src="https://avatars.githubusercontent.com/u/101121010?s=256" width="128" alt="Phplrt" />
    </a>
</p>

<p align="center">
    <a href="https://github.com/php-ffi-libs/glfw3-headers/actions"><img src="https://github.com/php-ffi-libs/glfw3-headers/workflows/build/badge.svg"></a>
    <a href="https://packagist.org/packages/ffi-libs/glfw3-headers"><img src="https://img.shields.io/badge/PHP-8.1+-ff0140.svg" alt="PHP 7.1+"></a>
    <a href="https://packagist.org/packages/ffi-libs/glfw3-headers"><img src="https://poser.pugx.org/ffi-libs/glfw3-headers/version" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/ffi-libs/glfw3-headers"><img src="https://poser.pugx.org/ffi-libs/glfw3-headers/v/unstable" alt="Latest Unstable Version"></a>
    <a href="https://packagist.org/packages/ffi-libs/glfw3-headers"><img src="https://poser.pugx.org/ffi-libs/glfw3-headers/downloads" alt="Total Downloads"></a>
    <a href="https://raw.githubusercontent.com/php-ffi-libs/glfw3-headers/master/LICENSE.md"><img src="https://poser.pugx.org/ffi-libs/glfw3-headers/license" alt="License MIT"></a>
</p>

# GLFW3 Headers

This is a C headers of the [GLFW3](https://www.glfw.org/) adopted for PHP.

## Requirements

- PHP >= 8.1

## Installation

Library is available as composer repository and can be installed using the
following command in a root of your project.

```sh
$ composer require ffi-libs/glfw3-headers
```

## Usage

```php
use FFI\Headers\SDL2;

$headers = SDL2::create(
    GLFW3\WindowPlatform::X11,  // Window API
    GLFW3\ContextPlatform::GLX, // Context API
    GLFW3\Version::V3_3_6,      // GLFW Headers Version
);

echo $headers;
```


