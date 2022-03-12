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

final class ContentRenderingTestCase extends TestCase
{
    /**
     * @testdox Testing that the headers are successfully built
     *
     * @dataProvider configDataProvider
     */
    public function testRenderable(Platform $platform, Version $version): void
    {
        $this->expectNotToPerformAssertions();

        (string)SDL2::create($platform, $version);
    }

    /**
     * @testdox Testing that headers contain correct syntax
     *
     * @depends testRenderable
     * @dataProvider configDataProvider
     */
    public function testCompilation(Platform $platform, Version $version): void
    {
        $this->assertHeadersSyntaxValid(
            SDL2::create($platform, $version)
        );
    }
}
