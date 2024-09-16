<?php

use Illuminate\Routing\Router;

$router->group(['prefix' => '/profile/v1'], function (Router $router) {
    //======  AUTH  <---
    require('ApiRoutes/authRoutes.php');
    //======  USERS
    require('ApiRoutes/userRoutes.php');
    //======  APP
    require('ApiRoutes/appRoutes.php');

    $router->apiCrud([
        'module' => 'iprofile',
        'prefix' => 'addresses',
        'controller' => 'AddressApiController',
        'permission' => 'profile.addresses'
        //'middleware' => ['create' => [], 'index' => [], 'show' => [], 'update' => [], 'delete' => [], 'restore' => []],
        // 'customRoutes' => [ // Include custom routes if needed
        //  [
        //    'method' => 'post', // get,post,put....
        //    'path' => '/some-path', // Route Path
        //    'uses' => 'ControllerMethodName', //Name of the controller method to use
        //    'middleware' => [] // if not set up middleware, auth:api will be the default
        //  ]
        // ]
    ]);
    $router->apiCrud([
        'module' => 'iprofile',
        'prefix' => 'departments',
        'controller' => 'DepartmentApiController',
        'permission' => 'profile.departments',
        'middleware' => ['index' => [], 'show' => []],
        // 'customRoutes' => [ // Include custom routes if needed
        //  [
        //    'method' => 'post', // get,post,put....
        //    'path' => '/some-path', // Route Path
        //    'uses' => 'ControllerMethodName', //Name of the controller method to use
        //    'middleware' => [] // if not set up middleware, auth:api will be the default
        //  ]
        // ]
    ]);
    $router->apiCrud([
        'module' => 'iprofile',
        'prefix' => 'fields',
        'controller' => 'FieldApiController',
        'permission' => 'profile.fields'
        //'middleware' => ['create' => [], 'index' => [], 'show' => [], 'update' => [], 'delete' => [], 'restore' => []],
        // 'customRoutes' => [ // Include custom routes if needed
        //  [
        //    'method' => 'post', // get,post,put....
        //    'path' => '/some-path', // Route Path
        //    'uses' => 'ControllerMethodName', //Name of the controller method to use
        //    'middleware' => [] // if not set up middleware, auth:api will be the default
        //  ]
        // ]
    ]);
    $router->apiCrud([
        'module' => 'iprofile',
        'prefix' => 'provider-accounts',
        'controller' => 'ProviderAccountApiController',
        //'middleware' => ['create' => [], 'index' => [], 'show' => [], 'update' => [], 'delete' => [], 'restore' => []],
        // 'customRoutes' => [ // Include custom routes if needed
        //  [
        //    'method' => 'post', // get,post,put....
        //    'path' => '/some-path', // Route Path
        //    'uses' => 'ControllerMethodName', //Name of the controller method to use
        //    'middleware' => [] // if not set up middleware, auth:api will be the default
        //  ]
        // ]
    ]);
    $router->apiCrud([
        'module' => 'iprofile',
        'prefix' => 'roles',
        'controller' => 'RoleApiController',
        'permission' => 'profile.role',
        'middleware' => ['index' => [], 'show' => []],
        // 'customRoutes' => [ // Include custom routes if needed
        //  [
        //    'method' => 'post', // get,post,put....
        //    'path' => '/some-path', // Route Path
        //    'uses' => 'ControllerMethodName', //Name of the controller method to use
        //    'middleware' => [] // if not set up middleware, auth:api will be the default
        //  ]
        // ]
    ]);
    $router->apiCrud([
        'module' => 'iprofile',
        'prefix' => 'settings',
        'controller' => 'SettingApiController',
        'permission' => 'profile.settings'
        //'middleware' => ['create' => [], 'index' => [], 'show' => [], 'update' => [], 'delete' => [], 'restore' => []],
        // 'customRoutes' => [ // Include custom routes if needed
        //  [
        //    'method' => 'post', // get,post,put....
        //    'path' => '/some-path', // Route Path
        //    'uses' => 'ControllerMethodName', //Name of the controller method to use
        //    'middleware' => [] // if not set up middleware, auth:api will be the default
        //  ]
        // ]
    ]);
    $router->apiCrud([
        'module' => 'iprofile',
        'prefix' => 'userpasswordhistories',
        'controller' => 'UserPasswordHistoryApiController',
        //'middleware' => ['create' => [], 'index' => [], 'show' => [], 'update' => [], 'delete' => [], 'restore' => []],
        // 'customRoutes' => [ // Include custom routes if needed
        //  [
        //    'method' => 'post', // get,post,put....
        //    'path' => '/some-path', // Route Path
        //    'uses' => 'ControllerMethodName', //Name of the controller method to use
        //    'middleware' => [] // if not set up middleware, auth:api will be the default
        //  ]
        // ]
    ]);
// append


});