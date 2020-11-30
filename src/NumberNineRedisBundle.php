<?php

/*
 * This file is part of the NumberNine package.
 *
 * (c) William Arin <williamarin.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NumberNine\RedisBundle;

use NumberNine\Common\Bundle\BundleTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NumberNineRedisBundle extends Bundle
{
    use BundleTrait;

    protected function getAlias(): string
    {
        return 'numbernine_redis';
    }
}
