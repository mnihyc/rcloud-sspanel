<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\Auth as AuthService;
use App\Utils\Cookie;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use function in_array;
use function str_contains;
use function time;

final class User implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = AuthService::getUser();
        $path = $request->getUri()->getPath();

        if (! $user->isLogin) {
            if (str_contains($path, '/user/order/create')) {
                Cookie::set(['redir' => $path . '?' . $request->getUri()->getQuery()], time() + 3600);
            }

            return AppFactory::determineResponseFactory()->createResponse(302)->withHeader('Location', '/auth/login');
        }

        $bannedUserEnabledPages = ['/user/banned', '/user/logout'];

        if ($user->is_banned && ! in_array($path, $bannedUserEnabledPages)) {
            return AppFactory::determineResponseFactory()->createResponse(302)->withHeader('Location', '/user/banned');
        }
        
        $unlinkedUserEnabledPages = ['/user/must_link', '/user/must_link/token'];
        if ($user->im_type !== 4 && ! in_array($path, $unlinkedUserEnabledPages)) {
            return AppFactory::determineResponseFactory()->createResponse(302)->withHeader('Location', '/user/must_link');
        }

        $request = $request->withAttribute('user', $user);

        return $handler->handle($request);
    }
}
