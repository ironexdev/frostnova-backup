# Frost Nova 
_You don't always need a framework_

Project starter pack that provides Dependency Injection and Controllers to handle http requests.
- Dependency Injection (PSR-11 Container)
- Routing (PSR-15 Server Request, PSR-7 Response)
- Middleware Stack (PSR-15 Middleware)

**Request to Response path**
1) [public/index.php](https://github.com/ironexdev/frostnova-app/blob/master/public/index.php)
2) [src/bootstrap.php](https://github.com/ironexdev/frostnova-app/blob/master/src/bootstrap.php)
3) [config/config-di.php](https://github.com/ironexdev/frostnova-app/blob/14280ebad5e8cbbc92778337173bd872a00dd7ad/config/config-di.php#L39)
    - Middleware Stack
      - Request is first processed by [CORS middleware](https://github.com/tuupola/cors-middleware) and then it is passed to Router
4) [src/Core/Router.php](https://github.com/ironexdev/frostnova-app/blob/master/src/Core/Router.php)
    - Request method and path is matched against route definition in [config/api/base/config.php](https://github.com/ironexdev/frostnova-app/blob/master/config/api/base/routes.php)

**Dependency Injection**
- Defined in [config/config-di.php](https://github.com/ironexdev/frostnova-app/blob/master/config/config-di.php)

**Routing**
- Defined in [config/api/base/config.php](https://github.com/ironexdev/frostnova-app/blob/master/config/api/base/routes.php)

## Development Environment

Development environment for this project https://github.com/ironexdev/frostnova
- It contains current repository and another "docker" repository as a git submodule
