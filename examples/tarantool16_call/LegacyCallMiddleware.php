<?php

/**
 * This file is part of the Tarantool Client package.
 *
 * (c) Eugene Leonovich <gen.work@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App;

use Tarantool\Client\Handler\Handler;
use Tarantool\Client\Middleware\Middleware;
use Tarantool\Client\Request\CallRequest;
use Tarantool\Client\Request\Request;
use Tarantool\Client\Response;

final class LegacyCallMiddleware implements Middleware
{
    public function process(Request $request, Handler $handler) : Response
    {
        if (!$request instanceof CallRequest) {
            return $handler->handle($request);
        }

        return $handler->handle(LegacyCallRequest::fromCallRequest($request));
    }
}