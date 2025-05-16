<?php

/*
|--------------------------------------------------------------------------
| Load The Cached Routes
|--------------------------------------------------------------------------
|
| Here we will decode and unserialize the RouteCollection instance that
| holds all of the route information for an application. This allows
| us to instantaneously load the entire route map into the router.
|
*/

app('router')->setCompiledRoutes(
    array (
  'compiled' => 
  array (
    0 => false,
    1 => 
    array (
      '/api/csrf-cookie' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'sanctum.csrf-cookie',
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
      '/_ignition/health-check' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ignition.healthCheck',
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
      '/_ignition/execute-solution' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ignition.executeSolution',
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
      '/_ignition/update-config' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ignition.updateConfig',
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
      '/api/v1/login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::IY7MbhqMiffdoVRg',
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
      '/api/v1/register' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::itQkKN5Zx7EwBEA5',
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
      '/api/v1/password/forgot' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Db6TNciOSCUQQicH',
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
      '/api/v1/login/judge' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::xpXASaZ0VCI2Z0Qi',
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
      '/api/v1/auth/google/redirect' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Y3LOP5lB0FOkvNDG',
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
      '/api/v1/auth/google/callback' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::x2VCFFQtuK8i2V94',
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
      '/api/v1/logout' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::D9x1UHJbLMLZvUDw',
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
      '/api/v1/users' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::eXIKGb9LEpYvBYgI',
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
            '_route' => 'generated::74EKLKy8Zslaj76L',
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
      '/api/v1/user' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::K99wdVTKIBEp1NBM',
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
      '/api/v1/events/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::tcVIweTxloO6CPjP',
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
      '/api/v1/events' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::WQd7iJAfwsBWwpAw',
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
      '/api/v1/judge/current-session' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::NrRMIV67YCHdpoCU',
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
      '/api/v1/judge/scoring-session' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::G8a5BK1GEV4cizhJ',
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
      '/api/v1/judge/submit-score' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::fyMdsXqVImn21DM8',
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
      '/api/v1/judge/confirm-score' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ZwmslMPiyPodf7Wp',
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
      '/api/v1/judge/final-results' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::S8VtDHrLWBEHeD9n',
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
            '_route' => 'generated::QsVj9xVNaeSerTbl',
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
      0 => '{^(?|/api/v1/(?|users/([^/]++)(?|(*:35))|events/([^/]++)(?|(*:61)|/(?|edit(*:76)|s(?|ta(?|rt(*:94)|ges(?|(*:107)|/(?|create(*:125)|([^/]++)(?|(*:144)|/(?|edit(*:160)|s(?|tart(*:176)|elect\\-top\\-candidates(*:206))|finalize(*:223)|reset(?|(*:239)|\\-top\\-candidates(*:264))|partial\\-results(*:289))|(*:298)))))|cores(?|(*:318)|/(?|create(*:336)|show(*:348)|edit/([^/]++)/([^/]++)/([^/]++)(*:387)|delete(*:401))))|finalize(*:420)|re(?|s(?|et(*:439)|ults/preview(*:459))|port(*:472))|ca(?|tegories(?|/(?|pending\\-scores(*:516)|create(*:530)|([^/]++)(?|(*:549)|/(?|edit(*:565)|s(?|tart(*:581)|et\\-candidate(*:602))|finalize(*:619)|reset(*:632)|pending\\-scores(*:655))|(*:664)))|(*:674))|ndidates(?|(*:694)|/(?|create(*:712)|([^/]++)(?|(*:731)|/edit(*:744)|(*:752)))))|judges(?|(*:773)|/(?|create(*:791)|([^/]++)(?|(*:810)|/edit(*:823)|(*:831)))))|(*:843))))/?$}sDu',
    ),
    3 => 
    array (
      35 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::FkhvLKFRGgRFpkvE',
          ),
          1 => 
          array (
            0 => 'user_id',
          ),
          2 => 
          array (
            'PATCH' => 0,
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
            '_route' => 'generated::aU2kblq5SCgV7lhV',
          ),
          1 => 
          array (
            0 => 'user_id',
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
      61 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::tTdnFQnTCdyQWHBX',
          ),
          1 => 
          array (
            0 => 'event_id',
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
      76 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::d291uLbp3zxvtOt1',
          ),
          1 => 
          array (
            0 => 'event_id',
          ),
          2 => 
          array (
            'PATCH' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      94 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::zALiA80OwjjWaDKC',
          ),
          1 => 
          array (
            0 => 'event_id',
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
      107 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::H0Btw3963bfAXPa5',
          ),
          1 => 
          array (
            0 => 'event_id',
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
      125 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::8QRvngfhJpF6nGQm',
          ),
          1 => 
          array (
            0 => 'event_id',
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
      144 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::UrXwBc5pOdaIzTjw',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'stage_id',
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
      160 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::OHbeYT0bPCdpSb4B',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'stage_id',
          ),
          2 => 
          array (
            'PATCH' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      176 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::CSsJoPKGirk0F5dw',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'stage_id',
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
      206 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::cxeF4TCcsZh5IPdE',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'stage_id',
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
      223 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::rntHxxGCFWud0zdw',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'stage_id',
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
      239 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::rXanMJUFh5E9YBLC',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'stage_id',
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
      264 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::wwfLXtPjJsKhbSW6',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'stage_id',
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
      289 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Sz45f8NSa64uXi4c',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'stage_id',
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
      298 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::9RuhrYI2D16qkzJT',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'stage_id',
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
      318 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::oTarq4kHhgzBfy0E',
          ),
          1 => 
          array (
            0 => 'event_id',
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
      336 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::WtMd5hEVmDvCNwny',
          ),
          1 => 
          array (
            0 => 'event_id',
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
      348 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::AtPdvJ51SunZ83MH',
          ),
          1 => 
          array (
            0 => 'event_id',
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
      387 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::zHq4fLE0pqChwXiy',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'judge_id',
            2 => 'candidate_id',
            3 => 'category_id',
          ),
          2 => 
          array (
            'PATCH' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      401 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::etHDcKxwdHAReMqR',
          ),
          1 => 
          array (
            0 => 'event_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      420 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::vSsSmG91lmUgpEQp',
          ),
          1 => 
          array (
            0 => 'event_id',
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
      439 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::NcKkqPwJ6xQlqQtk',
          ),
          1 => 
          array (
            0 => 'event_id',
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
      459 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::o6UZnAYxtIYs4ody',
          ),
          1 => 
          array (
            0 => 'event_id',
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
      472 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::T6R99zKdqp4BpoIb',
          ),
          1 => 
          array (
            0 => 'event_id',
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
      516 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::rP5uJJ4nWQzvr6qu',
          ),
          1 => 
          array (
            0 => 'event_id',
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
      530 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::p7IelmmihUWkvU4t',
          ),
          1 => 
          array (
            0 => 'event_id',
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
      549 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::PzkYdl8GSwFctSoM',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'category_id',
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
      565 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::njVwd4fkBGtXb74z',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'category_id',
          ),
          2 => 
          array (
            'PATCH' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      581 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::uefoFwU1hoGYLc1Q',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'category_id',
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
      602 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::2TkgF3f059ahEqqh',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'category_id',
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
      619 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::XmtuLy2VcnmHar7H',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'category_id',
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
      632 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::We6TVnSMK4poKqks',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'category_id',
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
      655 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::OO90yHQ7w6DUubrM',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'category_id',
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
      664 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::7cYpNaKAV6ZSzprS',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'category_id',
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
      674 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::JvS2afLoDQ64q2i4',
          ),
          1 => 
          array (
            0 => 'event_id',
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
      694 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::hF4MHcqmDypZPWvX',
          ),
          1 => 
          array (
            0 => 'event_id',
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
      712 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::kEUJbiphfdbDag3t',
          ),
          1 => 
          array (
            0 => 'event_id',
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
      731 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::xAnRzza1BWyTJtlb',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'candidate_id',
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
      744 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::M2R2v4W6qfrkXXFh',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'candidate_id',
          ),
          2 => 
          array (
            'PATCH' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      752 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::F4qzzvZ0ZNEmgXFA',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'candidate_id',
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
      773 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::0Q8uhgDI12YymydQ',
          ),
          1 => 
          array (
            0 => 'event_id',
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
      791 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::uHs80kVMntxVeJyL',
          ),
          1 => 
          array (
            0 => 'event_id',
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
      810 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::DKL38ktp2eggJwh9',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'judge_id',
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
      823 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::MIbxtrbflMSk1QC6',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'judge_id',
          ),
          2 => 
          array (
            'PATCH' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      831 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::caOgnSEQG0pGZZg9',
          ),
          1 => 
          array (
            0 => 'event_id',
            1 => 'judge_id',
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
      843 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::2eB3t5Cah6j6z4Zq',
          ),
          1 => 
          array (
            0 => 'event_id',
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
    'sanctum.csrf-cookie' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/csrf-cookie',
      'action' => 
      array (
        'uses' => 'Laravel\\Sanctum\\Http\\Controllers\\CsrfCookieController@show',
        'controller' => 'Laravel\\Sanctum\\Http\\Controllers\\CsrfCookieController@show',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'sanctum.csrf-cookie',
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
    'ignition.healthCheck' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => '_ignition/health-check',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'Spatie\\LaravelIgnition\\Http\\Middleware\\RunnableSolutionsEnabled',
        ),
        'uses' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\HealthCheckController@__invoke',
        'controller' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\HealthCheckController',
        'as' => 'ignition.healthCheck',
        'namespace' => NULL,
        'prefix' => '_ignition',
        'where' => 
        array (
        ),
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
    'ignition.executeSolution' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => '_ignition/execute-solution',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'Spatie\\LaravelIgnition\\Http\\Middleware\\RunnableSolutionsEnabled',
        ),
        'uses' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\ExecuteSolutionController@__invoke',
        'controller' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\ExecuteSolutionController',
        'as' => 'ignition.executeSolution',
        'namespace' => NULL,
        'prefix' => '_ignition',
        'where' => 
        array (
        ),
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
    'ignition.updateConfig' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => '_ignition/update-config',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'Spatie\\LaravelIgnition\\Http\\Middleware\\RunnableSolutionsEnabled',
        ),
        'uses' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\UpdateConfigController@__invoke',
        'controller' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\UpdateConfigController',
        'as' => 'ignition.updateConfig',
        'namespace' => NULL,
        'prefix' => '_ignition',
        'where' => 
        array (
        ),
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
    'generated::IY7MbhqMiffdoVRg' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\AuthController@login',
        'controller' => 'App\\Http\\Controllers\\AuthController@login',
        'namespace' => NULL,
        'prefix' => 'api/v1',
        'where' => 
        array (
        ),
        'as' => 'generated::IY7MbhqMiffdoVRg',
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
    'generated::itQkKN5Zx7EwBEA5' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/register',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\AuthController@register',
        'controller' => 'App\\Http\\Controllers\\AuthController@register',
        'namespace' => NULL,
        'prefix' => 'api/v1',
        'where' => 
        array (
        ),
        'as' => 'generated::itQkKN5Zx7EwBEA5',
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
    'generated::Db6TNciOSCUQQicH' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/password/forgot',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\AuthController@forgotPassword',
        'controller' => 'App\\Http\\Controllers\\AuthController@forgotPassword',
        'namespace' => NULL,
        'prefix' => 'api/v1',
        'where' => 
        array (
        ),
        'as' => 'generated::Db6TNciOSCUQQicH',
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
    'generated::xpXASaZ0VCI2Z0Qi' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/login/judge',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\AuthController@judgeLogin',
        'controller' => 'App\\Http\\Controllers\\AuthController@judgeLogin',
        'namespace' => NULL,
        'prefix' => 'api/v1',
        'where' => 
        array (
        ),
        'as' => 'generated::xpXASaZ0VCI2Z0Qi',
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
    'generated::Y3LOP5lB0FOkvNDG' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/auth/google/redirect',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\AuthController@redirectToGoogle',
        'controller' => 'App\\Http\\Controllers\\AuthController@redirectToGoogle',
        'namespace' => NULL,
        'prefix' => 'api/v1',
        'where' => 
        array (
        ),
        'as' => 'generated::Y3LOP5lB0FOkvNDG',
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
    'generated::x2VCFFQtuK8i2V94' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/auth/google/callback',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\AuthController@handleGoogleCallback',
        'controller' => 'App\\Http\\Controllers\\AuthController@handleGoogleCallback',
        'namespace' => NULL,
        'prefix' => 'api/v1',
        'where' => 
        array (
        ),
        'as' => 'generated::x2VCFFQtuK8i2V94',
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
    'generated::D9x1UHJbLMLZvUDw' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/logout',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\AuthController@logout',
        'controller' => 'App\\Http\\Controllers\\AuthController@logout',
        'namespace' => NULL,
        'prefix' => 'api/v1',
        'where' => 
        array (
        ),
        'as' => 'generated::D9x1UHJbLMLZvUDw',
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
    'generated::eXIKGb9LEpYvBYgI' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/users',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\UserController@index',
        'controller' => 'App\\Http\\Controllers\\UserController@index',
        'namespace' => NULL,
        'prefix' => 'api/v1',
        'where' => 
        array (
        ),
        'as' => 'generated::eXIKGb9LEpYvBYgI',
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
    'generated::K99wdVTKIBEp1NBM' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/user',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\UserController@show',
        'controller' => 'App\\Http\\Controllers\\UserController@show',
        'namespace' => NULL,
        'prefix' => 'api/v1',
        'where' => 
        array (
        ),
        'as' => 'generated::K99wdVTKIBEp1NBM',
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
    'generated::74EKLKy8Zslaj76L' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/users',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\UserController@store',
        'controller' => 'App\\Http\\Controllers\\UserController@store',
        'namespace' => NULL,
        'prefix' => 'api/v1',
        'where' => 
        array (
        ),
        'as' => 'generated::74EKLKy8Zslaj76L',
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
    'generated::FkhvLKFRGgRFpkvE' => 
    array (
      'methods' => 
      array (
        0 => 'PATCH',
      ),
      'uri' => 'api/v1/users/{user_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\UserController@update',
        'controller' => 'App\\Http\\Controllers\\UserController@update',
        'namespace' => NULL,
        'prefix' => 'api/v1',
        'where' => 
        array (
        ),
        'as' => 'generated::FkhvLKFRGgRFpkvE',
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
    'generated::aU2kblq5SCgV7lhV' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'api/v1/users/{user_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\UserController@destroy',
        'controller' => 'App\\Http\\Controllers\\UserController@destroy',
        'namespace' => NULL,
        'prefix' => 'api/v1',
        'where' => 
        array (
        ),
        'as' => 'generated::aU2kblq5SCgV7lhV',
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
    'generated::tcVIweTxloO6CPjP' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/events/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\EventController@store',
        'controller' => 'App\\Http\\Controllers\\EventController@store',
        'namespace' => NULL,
        'prefix' => 'api/v1/events',
        'where' => 
        array (
        ),
        'as' => 'generated::tcVIweTxloO6CPjP',
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
    'generated::WQd7iJAfwsBWwpAw' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/events',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\EventController@index',
        'controller' => 'App\\Http\\Controllers\\EventController@index',
        'namespace' => NULL,
        'prefix' => 'api/v1/events',
        'where' => 
        array (
        ),
        'as' => 'generated::WQd7iJAfwsBWwpAw',
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
    'generated::tTdnFQnTCdyQWHBX' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/events/{event_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\EventController@show',
        'controller' => 'App\\Http\\Controllers\\EventController@show',
        'namespace' => NULL,
        'prefix' => 'api/v1/events',
        'where' => 
        array (
        ),
        'as' => 'generated::tTdnFQnTCdyQWHBX',
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
    'generated::d291uLbp3zxvtOt1' => 
    array (
      'methods' => 
      array (
        0 => 'PATCH',
      ),
      'uri' => 'api/v1/events/{event_id}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\EventController@update',
        'controller' => 'App\\Http\\Controllers\\EventController@update',
        'namespace' => NULL,
        'prefix' => 'api/v1/events',
        'where' => 
        array (
        ),
        'as' => 'generated::d291uLbp3zxvtOt1',
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
    'generated::2eB3t5Cah6j6z4Zq' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'api/v1/events/{event_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\EventController@destroy',
        'controller' => 'App\\Http\\Controllers\\EventController@destroy',
        'namespace' => NULL,
        'prefix' => 'api/v1/events',
        'where' => 
        array (
        ),
        'as' => 'generated::2eB3t5Cah6j6z4Zq',
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
    'generated::zALiA80OwjjWaDKC' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/events/{event_id}/start',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\EventController@start',
        'controller' => 'App\\Http\\Controllers\\EventController@start',
        'namespace' => NULL,
        'prefix' => 'api/v1/events',
        'where' => 
        array (
        ),
        'as' => 'generated::zALiA80OwjjWaDKC',
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
    'generated::vSsSmG91lmUgpEQp' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/events/{event_id}/finalize',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\EventController@finalize',
        'controller' => 'App\\Http\\Controllers\\EventController@finalize',
        'namespace' => NULL,
        'prefix' => 'api/v1/events',
        'where' => 
        array (
        ),
        'as' => 'generated::vSsSmG91lmUgpEQp',
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
    'generated::NcKkqPwJ6xQlqQtk' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/events/{event_id}/reset',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\EventController@reset',
        'controller' => 'App\\Http\\Controllers\\EventController@reset',
        'namespace' => NULL,
        'prefix' => 'api/v1/events',
        'where' => 
        array (
        ),
        'as' => 'generated::NcKkqPwJ6xQlqQtk',
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
    'generated::H0Btw3963bfAXPa5' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/events/{event_id}/stages',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\StageController@index',
        'controller' => 'App\\Http\\Controllers\\StageController@index',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/stages',
        'where' => 
        array (
        ),
        'as' => 'generated::H0Btw3963bfAXPa5',
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
    'generated::8QRvngfhJpF6nGQm' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/events/{event_id}/stages/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\StageController@store',
        'controller' => 'App\\Http\\Controllers\\StageController@store',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/stages',
        'where' => 
        array (
        ),
        'as' => 'generated::8QRvngfhJpF6nGQm',
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
    'generated::UrXwBc5pOdaIzTjw' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/events/{event_id}/stages/{stage_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\StageController@show',
        'controller' => 'App\\Http\\Controllers\\StageController@show',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/stages',
        'where' => 
        array (
        ),
        'as' => 'generated::UrXwBc5pOdaIzTjw',
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
    'generated::OHbeYT0bPCdpSb4B' => 
    array (
      'methods' => 
      array (
        0 => 'PATCH',
      ),
      'uri' => 'api/v1/events/{event_id}/stages/{stage_id}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\StageController@update',
        'controller' => 'App\\Http\\Controllers\\StageController@update',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/stages',
        'where' => 
        array (
        ),
        'as' => 'generated::OHbeYT0bPCdpSb4B',
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
    'generated::9RuhrYI2D16qkzJT' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'api/v1/events/{event_id}/stages/{stage_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\StageController@destroy',
        'controller' => 'App\\Http\\Controllers\\StageController@destroy',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/stages',
        'where' => 
        array (
        ),
        'as' => 'generated::9RuhrYI2D16qkzJT',
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
    'generated::CSsJoPKGirk0F5dw' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/events/{event_id}/stages/{stage_id}/start',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\StageController@start',
        'controller' => 'App\\Http\\Controllers\\StageController@start',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/stages',
        'where' => 
        array (
        ),
        'as' => 'generated::CSsJoPKGirk0F5dw',
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
    'generated::rntHxxGCFWud0zdw' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/events/{event_id}/stages/{stage_id}/finalize',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\StageController@finalize',
        'controller' => 'App\\Http\\Controllers\\StageController@finalize',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/stages',
        'where' => 
        array (
        ),
        'as' => 'generated::rntHxxGCFWud0zdw',
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
    'generated::rXanMJUFh5E9YBLC' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/events/{event_id}/stages/{stage_id}/reset',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\StageController@reset',
        'controller' => 'App\\Http\\Controllers\\StageController@reset',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/stages',
        'where' => 
        array (
        ),
        'as' => 'generated::rXanMJUFh5E9YBLC',
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
    'generated::cxeF4TCcsZh5IPdE' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/events/{event_id}/stages/{stage_id}/select-top-candidates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\StageController@selectTopCandidates',
        'controller' => 'App\\Http\\Controllers\\StageController@selectTopCandidates',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/stages',
        'where' => 
        array (
        ),
        'as' => 'generated::cxeF4TCcsZh5IPdE',
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
    'generated::wwfLXtPjJsKhbSW6' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/events/{event_id}/stages/{stage_id}/reset-top-candidates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\StageController@resetTopCandidates',
        'controller' => 'App\\Http\\Controllers\\StageController@resetTopCandidates',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/stages',
        'where' => 
        array (
        ),
        'as' => 'generated::wwfLXtPjJsKhbSW6',
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
    'generated::Sz45f8NSa64uXi4c' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/events/{event_id}/stages/{stage_id}/partial-results',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\StageController@partialResults',
        'controller' => 'App\\Http\\Controllers\\StageController@partialResults',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/stages',
        'where' => 
        array (
        ),
        'as' => 'generated::Sz45f8NSa64uXi4c',
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
    'generated::rP5uJJ4nWQzvr6qu' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/events/{event_id}/categories/pending-scores',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\CategoryController@hasPendingScoresForAll',
        'controller' => 'App\\Http\\Controllers\\CategoryController@hasPendingScoresForAll',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/categories',
        'where' => 
        array (
        ),
        'as' => 'generated::rP5uJJ4nWQzvr6qu',
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
    'generated::JvS2afLoDQ64q2i4' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/events/{event_id}/categories',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\CategoryController@index',
        'controller' => 'App\\Http\\Controllers\\CategoryController@index',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/categories',
        'where' => 
        array (
        ),
        'as' => 'generated::JvS2afLoDQ64q2i4',
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
    'generated::p7IelmmihUWkvU4t' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/events/{event_id}/categories/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\CategoryController@store',
        'controller' => 'App\\Http\\Controllers\\CategoryController@store',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/categories',
        'where' => 
        array (
        ),
        'as' => 'generated::p7IelmmihUWkvU4t',
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
    'generated::PzkYdl8GSwFctSoM' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/events/{event_id}/categories/{category_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\CategoryController@show',
        'controller' => 'App\\Http\\Controllers\\CategoryController@show',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/categories',
        'where' => 
        array (
        ),
        'as' => 'generated::PzkYdl8GSwFctSoM',
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
    'generated::njVwd4fkBGtXb74z' => 
    array (
      'methods' => 
      array (
        0 => 'PATCH',
      ),
      'uri' => 'api/v1/events/{event_id}/categories/{category_id}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\CategoryController@update',
        'controller' => 'App\\Http\\Controllers\\CategoryController@update',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/categories',
        'where' => 
        array (
        ),
        'as' => 'generated::njVwd4fkBGtXb74z',
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
    'generated::7cYpNaKAV6ZSzprS' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'api/v1/events/{event_id}/categories/{category_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\CategoryController@destroy',
        'controller' => 'App\\Http\\Controllers\\CategoryController@destroy',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/categories',
        'where' => 
        array (
        ),
        'as' => 'generated::7cYpNaKAV6ZSzprS',
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
    'generated::uefoFwU1hoGYLc1Q' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/events/{event_id}/categories/{category_id}/start',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\CategoryController@start',
        'controller' => 'App\\Http\\Controllers\\CategoryController@start',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/categories',
        'where' => 
        array (
        ),
        'as' => 'generated::uefoFwU1hoGYLc1Q',
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
    'generated::XmtuLy2VcnmHar7H' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/events/{event_id}/categories/{category_id}/finalize',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\CategoryController@finalize',
        'controller' => 'App\\Http\\Controllers\\CategoryController@finalize',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/categories',
        'where' => 
        array (
        ),
        'as' => 'generated::XmtuLy2VcnmHar7H',
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
    'generated::We6TVnSMK4poKqks' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/events/{event_id}/categories/{category_id}/reset',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\CategoryController@reset',
        'controller' => 'App\\Http\\Controllers\\CategoryController@reset',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/categories',
        'where' => 
        array (
        ),
        'as' => 'generated::We6TVnSMK4poKqks',
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
    'generated::2TkgF3f059ahEqqh' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/events/{event_id}/categories/{category_id}/set-candidate',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\CategoryController@setCandidate',
        'controller' => 'App\\Http\\Controllers\\CategoryController@setCandidate',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/categories',
        'where' => 
        array (
        ),
        'as' => 'generated::2TkgF3f059ahEqqh',
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
    'generated::OO90yHQ7w6DUubrM' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/events/{event_id}/categories/{category_id}/pending-scores',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\CategoryController@hasPendingScores',
        'controller' => 'App\\Http\\Controllers\\CategoryController@hasPendingScores',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/categories',
        'where' => 
        array (
        ),
        'as' => 'generated::OO90yHQ7w6DUubrM',
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
    'generated::hF4MHcqmDypZPWvX' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/events/{event_id}/candidates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\CandidateController@index',
        'controller' => 'App\\Http\\Controllers\\CandidateController@index',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/candidates',
        'where' => 
        array (
        ),
        'as' => 'generated::hF4MHcqmDypZPWvX',
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
    'generated::kEUJbiphfdbDag3t' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/events/{event_id}/candidates/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\CandidateController@store',
        'controller' => 'App\\Http\\Controllers\\CandidateController@store',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/candidates',
        'where' => 
        array (
        ),
        'as' => 'generated::kEUJbiphfdbDag3t',
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
    'generated::xAnRzza1BWyTJtlb' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/events/{event_id}/candidates/{candidate_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\CandidateController@show',
        'controller' => 'App\\Http\\Controllers\\CandidateController@show',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/candidates',
        'where' => 
        array (
        ),
        'as' => 'generated::xAnRzza1BWyTJtlb',
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
    'generated::M2R2v4W6qfrkXXFh' => 
    array (
      'methods' => 
      array (
        0 => 'PATCH',
      ),
      'uri' => 'api/v1/events/{event_id}/candidates/{candidate_id}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\CandidateController@update',
        'controller' => 'App\\Http\\Controllers\\CandidateController@update',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/candidates',
        'where' => 
        array (
        ),
        'as' => 'generated::M2R2v4W6qfrkXXFh',
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
    'generated::F4qzzvZ0ZNEmgXFA' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'api/v1/events/{event_id}/candidates/{candidate_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\CandidateController@destroy',
        'controller' => 'App\\Http\\Controllers\\CandidateController@destroy',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/candidates',
        'where' => 
        array (
        ),
        'as' => 'generated::F4qzzvZ0ZNEmgXFA',
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
    'generated::0Q8uhgDI12YymydQ' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/events/{event_id}/judges',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\JudgeController@index',
        'controller' => 'App\\Http\\Controllers\\JudgeController@index',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/judges',
        'where' => 
        array (
        ),
        'as' => 'generated::0Q8uhgDI12YymydQ',
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
    'generated::uHs80kVMntxVeJyL' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/events/{event_id}/judges/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\JudgeController@store',
        'controller' => 'App\\Http\\Controllers\\JudgeController@store',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/judges',
        'where' => 
        array (
        ),
        'as' => 'generated::uHs80kVMntxVeJyL',
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
    'generated::DKL38ktp2eggJwh9' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/events/{event_id}/judges/{judge_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\JudgeController@show',
        'controller' => 'App\\Http\\Controllers\\JudgeController@show',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/judges',
        'where' => 
        array (
        ),
        'as' => 'generated::DKL38ktp2eggJwh9',
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
    'generated::MIbxtrbflMSk1QC6' => 
    array (
      'methods' => 
      array (
        0 => 'PATCH',
      ),
      'uri' => 'api/v1/events/{event_id}/judges/{judge_id}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\JudgeController@update',
        'controller' => 'App\\Http\\Controllers\\JudgeController@update',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/judges',
        'where' => 
        array (
        ),
        'as' => 'generated::MIbxtrbflMSk1QC6',
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
    'generated::caOgnSEQG0pGZZg9' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'api/v1/events/{event_id}/judges/{judge_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\JudgeController@destroy',
        'controller' => 'App\\Http\\Controllers\\JudgeController@destroy',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/judges',
        'where' => 
        array (
        ),
        'as' => 'generated::caOgnSEQG0pGZZg9',
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
    'generated::oTarq4kHhgzBfy0E' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/events/{event_id}/scores',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\ScoreController@index',
        'controller' => 'App\\Http\\Controllers\\ScoreController@index',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/scores',
        'where' => 
        array (
        ),
        'as' => 'generated::oTarq4kHhgzBfy0E',
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
    'generated::WtMd5hEVmDvCNwny' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/events/{event_id}/scores/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\ScoreController@store',
        'controller' => 'App\\Http\\Controllers\\ScoreController@store',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/scores',
        'where' => 
        array (
        ),
        'as' => 'generated::WtMd5hEVmDvCNwny',
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
    'generated::AtPdvJ51SunZ83MH' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/events/{event_id}/scores/show',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\ScoreController@show',
        'controller' => 'App\\Http\\Controllers\\ScoreController@show',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/scores',
        'where' => 
        array (
        ),
        'as' => 'generated::AtPdvJ51SunZ83MH',
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
    'generated::zHq4fLE0pqChwXiy' => 
    array (
      'methods' => 
      array (
        0 => 'PATCH',
      ),
      'uri' => 'api/v1/events/{event_id}/scores/edit/{judge_id}/{candidate_id}/{category_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\ScoreController@update',
        'controller' => 'App\\Http\\Controllers\\ScoreController@update',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/scores',
        'where' => 
        array (
        ),
        'as' => 'generated::zHq4fLE0pqChwXiy',
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
    'generated::etHDcKxwdHAReMqR' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'api/v1/events/{event_id}/scores/delete',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\ScoreController@destroy',
        'controller' => 'App\\Http\\Controllers\\ScoreController@destroy',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}/scores',
        'where' => 
        array (
        ),
        'as' => 'generated::etHDcKxwdHAReMqR',
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
    'generated::T6R99zKdqp4BpoIb' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/events/{event_id}/report',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\PdfReportController@download',
        'controller' => 'App\\Http\\Controllers\\PdfReportController@download',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}',
        'where' => 
        array (
        ),
        'as' => 'generated::T6R99zKdqp4BpoIb',
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
    'generated::o6UZnAYxtIYs4ody' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/events/{event_id}/results/preview',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:admin,tabulator',
        ),
        'uses' => 'App\\Http\\Controllers\\PdfReportController@preview',
        'controller' => 'App\\Http\\Controllers\\PdfReportController@preview',
        'namespace' => NULL,
        'prefix' => 'api/v1/events/{event_id}',
        'where' => 
        array (
        ),
        'as' => 'generated::o6UZnAYxtIYs4ody',
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
    'generated::NrRMIV67YCHdpoCU' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/judge/current-session',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:judge',
          3 => 'no.cache',
        ),
        'uses' => 'App\\Http\\Controllers\\JudgeController@currentSession',
        'controller' => 'App\\Http\\Controllers\\JudgeController@currentSession',
        'namespace' => NULL,
        'prefix' => 'api/v1/judge',
        'where' => 
        array (
        ),
        'as' => 'generated::NrRMIV67YCHdpoCU',
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
    'generated::G8a5BK1GEV4cizhJ' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/judge/scoring-session',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:judge',
          3 => 'no.cache',
        ),
        'uses' => 'App\\Http\\Controllers\\JudgeController@scoringSession',
        'controller' => 'App\\Http\\Controllers\\JudgeController@scoringSession',
        'namespace' => NULL,
        'prefix' => 'api/v1/judge',
        'where' => 
        array (
        ),
        'as' => 'generated::G8a5BK1GEV4cizhJ',
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
    'generated::fyMdsXqVImn21DM8' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/judge/submit-score',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:judge',
          3 => 'no.cache',
        ),
        'uses' => 'App\\Http\\Controllers\\ScoreController@submit',
        'controller' => 'App\\Http\\Controllers\\ScoreController@submit',
        'namespace' => NULL,
        'prefix' => 'api/v1/judge',
        'where' => 
        array (
        ),
        'as' => 'generated::fyMdsXqVImn21DM8',
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
    'generated::ZwmslMPiyPodf7Wp' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/judge/confirm-score',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:judge',
          3 => 'no.cache',
        ),
        'uses' => 'App\\Http\\Controllers\\ScoreController@confirm',
        'controller' => 'App\\Http\\Controllers\\ScoreController@confirm',
        'namespace' => NULL,
        'prefix' => 'api/v1/judge',
        'where' => 
        array (
        ),
        'as' => 'generated::ZwmslMPiyPodf7Wp',
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
    'generated::S8VtDHrLWBEHeD9n' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/judge/final-results',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:judge',
          3 => 'no.cache',
        ),
        'uses' => 'App\\Http\\Controllers\\ScoreController@finalResults',
        'controller' => 'App\\Http\\Controllers\\ScoreController@finalResults',
        'namespace' => NULL,
        'prefix' => 'api/v1/judge',
        'where' => 
        array (
        ),
        'as' => 'generated::S8VtDHrLWBEHeD9n',
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
    'generated::QsVj9xVNaeSerTbl' => 
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
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:43:"function () {
    return view(\'welcome\');
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"000000000000053c0000000000000000";}}',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::QsVj9xVNaeSerTbl',
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
  ),
)
);
