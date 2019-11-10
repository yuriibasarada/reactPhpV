<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 29.10.2019
 * Time: 22:21
 */

namespace App\Core;

use Psr\Http\Message\ServerRequestInterface;

class JsonRequestDecoder
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
       if ($request->getHeaderLine('Content-type') === 'application/json') {
          $request = $request->withParsedBody(
              json_decode($request->getBody()->getContents(), true)
          );
       }
       return $next($request);
    }
}
