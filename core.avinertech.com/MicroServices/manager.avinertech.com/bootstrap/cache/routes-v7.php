<?php

app('router')->setCompiledRoutes(
    array (
  'compiled' => 
  array (
    0 => false,
    1 => 
    array (
      '/api/manage-module' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::D6QiW8qcxWBIFFDi',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/up' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::mA1CcvaK1opcWW1v',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/api/modules' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'api.modules.create',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::O7HUQnFJAfii9QTq',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/tenants' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::DqpDgRbTlI4ifWEF',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::EtLdc1hlBIp3HDHM',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/tenants/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::dLi5fkv9mBItginv',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/up' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::DSNSCzPDA6u7yzUM',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::E0OfInmEFAq5chhP',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/tenants' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'tenants.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'tenants.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/tenants/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'tenants.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
    ),
    2 => 
    array (
      0 => '{^(?|/api/(?|tenant(?|/([^/]++)(?|(*:36)|/download(?:/([^/]++))?(*:66))|s/([^/]++)(?|/(?|edit(*:95)|toggle\\-(?|payment(*:120)|block(*:133))|create\\-(?|module(*:159)|database(*:175))|deploy\\-module(*:198)|ssl\\-cert(*:215))|(*:224)))|deployment/([^/]++)(*:253))|/tenants/([^/]++)(?|/(?|edit(*:290)|toggle\\-(?|payment(*:316)|block(*:329)))|(*:339))|/storage/(.*)(*:361))/?$}sDu',
    ),
    3 => 
    array (
      36 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::nNmGXdtM5lRzwKzD',
          ),
          1 => 
          array (
            0 => 'tenant_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      66 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::fl5NQl7OCehzVXAX',
            'format' => NULL,
          ),
          1 => 
          array (
            0 => 'tenant_id',
            1 => 'format',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      95 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::YgNrcRVSZy6tu8Dk',
          ),
          1 => 
          array (
            0 => 'tenant',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      120 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::etx5q1EDwYHmr0G3',
          ),
          1 => 
          array (
            0 => 'tenant',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      133 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::XBho3lJjbAPY5zwL',
          ),
          1 => 
          array (
            0 => 'tenant',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      159 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::jvGnTZhRU9Z3EzJk',
          ),
          1 => 
          array (
            0 => 'tenant',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      175 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::aTixoA9w54IJOOSM',
          ),
          1 => 
          array (
            0 => 'tenant',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      198 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::sNeQB8CitO6uuRFi',
          ),
          1 => 
          array (
            0 => 'tenant',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      215 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::uXWocgoEi4Z8nNLB',
          ),
          1 => 
          array (
            0 => 'tenant',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      224 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::veRUbHtDYjFELCgA',
          ),
          1 => 
          array (
            0 => 'tenant',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::PprILnDdVakxdFjV',
          ),
          1 => 
          array (
            0 => 'tenant',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      253 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::sGIwMeXXamjqnBbQ',
          ),
          1 => 
          array (
            0 => 'action',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      290 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'tenants.edit',
          ),
          1 => 
          array (
            0 => 'tenant',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      316 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'tenants.toggle-payment',
          ),
          1 => 
          array (
            0 => 'tenant',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      329 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'tenants.toggle-block',
          ),
          1 => 
          array (
            0 => 'tenant',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      339 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'tenants.update',
          ),
          1 => 
          array (
            0 => 'tenant',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'tenants.destroy',
          ),
          1 => 
          array (
            0 => 'tenant',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      361 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'storage.local',
          ),
          1 => 
          array (
            0 => 'path',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => NULL,
          1 => NULL,
          2 => NULL,
          3 => NULL,
          4 => false,
          5 => false,
          6 => 0,
        ),
      ),
    ),
    4 => NULL,
  ),
  'attributes' => 
  array (
    'generated::nNmGXdtM5lRzwKzD' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/tenant/{tenant_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\TenantSiteController@getTenantInfo',
        'controller' => 'App\\Http\\Controllers\\TenantSiteController@getTenantInfo',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::nNmGXdtM5lRzwKzD',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::fl5NQl7OCehzVXAX' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/tenant/{tenant_id}/download/{format?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\TenantSiteController@downloadTenantInfo',
        'controller' => 'App\\Http\\Controllers\\TenantSiteController@downloadTenantInfo',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::fl5NQl7OCehzVXAX',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::D6QiW8qcxWBIFFDi' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/manage-module',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\ManagerController@createApplication',
        'controller' => 'App\\Http\\Controllers\\ManagerController@createApplication',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::D6QiW8qcxWBIFFDi',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::mA1CcvaK1opcWW1v' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/up',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:121:"function (){
    return \\response()->json([
        \'status\' => true,
        \'message\' => \'backend is up\'
    ], 200);
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000004f00000000000000000";}}',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::mA1CcvaK1opcWW1v',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::sGIwMeXXamjqnBbQ' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/deployment/{action}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\DeploymentController@handleAction',
        'controller' => 'App\\Http\\Controllers\\DeploymentController@handleAction',
        'namespace' => NULL,
        'prefix' => 'api/deployment',
        'where' => 
        array (
        ),
        'as' => 'generated::sGIwMeXXamjqnBbQ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'api.modules.create' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/api/modules',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\DeploymentController@createModule',
        'controller' => 'App\\Http\\Controllers\\DeploymentController@createModule',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'api.modules.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::O7HUQnFJAfii9QTq' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\TenantController@index',
        'controller' => 'App\\Http\\Controllers\\TenantController@index',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::O7HUQnFJAfii9QTq',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::DqpDgRbTlI4ifWEF' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/tenants',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\TenantController@index',
        'controller' => 'App\\Http\\Controllers\\TenantController@index',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::DqpDgRbTlI4ifWEF',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::dLi5fkv9mBItginv' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/tenants/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\TenantController@create',
        'controller' => 'App\\Http\\Controllers\\TenantController@create',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::dLi5fkv9mBItginv',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::EtLdc1hlBIp3HDHM' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/tenants',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\TenantController@store',
        'controller' => 'App\\Http\\Controllers\\TenantController@store',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::EtLdc1hlBIp3HDHM',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::YgNrcRVSZy6tu8Dk' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/tenants/{tenant}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\TenantController@edit',
        'controller' => 'App\\Http\\Controllers\\TenantController@edit',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::YgNrcRVSZy6tu8Dk',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::veRUbHtDYjFELCgA' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'api/tenants/{tenant}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\TenantController@update',
        'controller' => 'App\\Http\\Controllers\\TenantController@update',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::veRUbHtDYjFELCgA',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::PprILnDdVakxdFjV' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'api/tenants/{tenant}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\TenantController@destroy',
        'controller' => 'App\\Http\\Controllers\\TenantController@destroy',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::PprILnDdVakxdFjV',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::etx5q1EDwYHmr0G3' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/tenants/{tenant}/toggle-payment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\TenantController@togglePayment',
        'controller' => 'App\\Http\\Controllers\\TenantController@togglePayment',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::etx5q1EDwYHmr0G3',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::XBho3lJjbAPY5zwL' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/tenants/{tenant}/toggle-block',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\TenantController@toggleBlock',
        'controller' => 'App\\Http\\Controllers\\TenantController@toggleBlock',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::XBho3lJjbAPY5zwL',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::jvGnTZhRU9Z3EzJk' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/tenants/{tenant}/create-module',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\DeploymentController@createModule',
        'controller' => 'App\\Http\\Controllers\\DeploymentController@createModule',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::jvGnTZhRU9Z3EzJk',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::sNeQB8CitO6uuRFi' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/tenants/{tenant}/deploy-module',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\DeploymentController@deployModule',
        'controller' => 'App\\Http\\Controllers\\DeploymentController@deployModule',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::sNeQB8CitO6uuRFi',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::uXWocgoEi4Z8nNLB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/tenants/{tenant}/ssl-cert',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\DeploymentController@sslCert',
        'controller' => 'App\\Http\\Controllers\\DeploymentController@sslCert',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::uXWocgoEi4Z8nNLB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::aTixoA9w54IJOOSM' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/tenants/{tenant}/create-database',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\DeploymentController@createDatabase',
        'controller' => 'App\\Http\\Controllers\\DeploymentController@createDatabase',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::aTixoA9w54IJOOSM',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::DSNSCzPDA6u7yzUM' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'up',
      'action' => 
      array (
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:831:"function () {
                    $exception = null;

                    try {
                        \\Illuminate\\Support\\Facades\\Event::dispatch(new \\Illuminate\\Foundation\\Events\\DiagnosingHealth);
                    } catch (\\Throwable $e) {
                        if (app()->hasDebugModeEnabled()) {
                            throw $e;
                        }

                        report($e);

                        $exception = $e->getMessage();
                    }

                    return response(\\Illuminate\\Support\\Facades\\View::file(\'/var/www/sites/project.avinertech.com/vendor/laravel/framework/src/Illuminate/Foundation/Configuration\'.\'/../resources/health-up.blade.php\', [
                        \'exception\' => $exception,
                    ]), status: $exception ? 500 : 200);
                }";s:5:"scope";s:54:"Illuminate\\Foundation\\Configuration\\ApplicationBuilder";s:4:"this";N;s:4:"self";s:32:"00000000000004eb0000000000000000";}}',
        'as' => 'generated::DSNSCzPDA6u7yzUM',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::E0OfInmEFAq5chhP' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => '/',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\TenantController@index',
        'controller' => 'App\\Http\\Controllers\\TenantController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::E0OfInmEFAq5chhP',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'tenants.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'tenants',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\TenantController@index',
        'controller' => 'App\\Http\\Controllers\\TenantController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'tenants.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'tenants.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'tenants/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\TenantController@create',
        'controller' => 'App\\Http\\Controllers\\TenantController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'tenants.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'tenants.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'tenants',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\TenantController@store',
        'controller' => 'App\\Http\\Controllers\\TenantController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'tenants.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'tenants.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'tenants/{tenant}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\TenantController@edit',
        'controller' => 'App\\Http\\Controllers\\TenantController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'tenants.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'tenants.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'tenants/{tenant}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\TenantController@update',
        'controller' => 'App\\Http\\Controllers\\TenantController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'tenants.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'tenants.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'tenants/{tenant}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\TenantController@destroy',
        'controller' => 'App\\Http\\Controllers\\TenantController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'tenants.destroy',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'tenants.toggle-payment' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'tenants/{tenant}/toggle-payment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\TenantController@togglePayment',
        'controller' => 'App\\Http\\Controllers\\TenantController@togglePayment',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'tenants.toggle-payment',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'tenants.toggle-block' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'tenants/{tenant}/toggle-block',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'App\\Http\\Middleware\\CheckAccessToken',
        ),
        'uses' => 'App\\Http\\Controllers\\TenantController@toggleBlock',
        'controller' => 'App\\Http\\Controllers\\TenantController@toggleBlock',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'tenants.toggle-block',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'storage.local' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'storage/{path}',
      'action' => 
      array (
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:3:{s:4:"disk";s:5:"local";s:6:"config";a:5:{s:6:"driver";s:5:"local";s:4:"root";s:114:"/var/www/sites/project.avinertech.com/core.avinertech.com/MicroServices/manager.avinertech.com/storage/app/private";s:5:"serve";b:1;s:5:"throw";b:0;s:6:"report";b:0;}s:12:"isProduction";b:0;}s:8:"function";s:323:"function (\\Illuminate\\Http\\Request $request, string $path) use ($disk, $config, $isProduction) {
                    return (new \\Illuminate\\Filesystem\\ServeFile(
                        $disk,
                        $config,
                        $isProduction
                    ))($request, $path);
                }";s:5:"scope";s:47:"Illuminate\\Filesystem\\FilesystemServiceProvider";s:4:"this";N;s:4:"self";s:32:"00000000000005030000000000000000";}}',
        'as' => 'storage.local',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'path' => '.*',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
  ),
)
);
