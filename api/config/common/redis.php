<?php

declare(strict_types=1);

use App\Data\RedisWrapper;

return [
    RedisWrapper::class => static fn (): RedisWrapper => new RedisWrapper('api-redis'),
];
