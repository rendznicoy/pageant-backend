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
            '_route' => 'generated::84aaGPvswjsoZlJk',
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
            '_route' => 'generated::F5ocOoDwCvWZEmqE',
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
            '_route' => 'generated::gs3PgRfj4LTfY63K',
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
            '_route' => 'generated::5ImC7Eatvbl1x0IU',
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
            '_route' => 'generated::gX8i4Ug04KTpp3Xf',
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
            '_route' => 'generated::CPqhwIRaRmyenTCG',
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
            '_route' => 'generated::tawrUeWnJ2hlyQ3O',
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
            '_route' => 'generated::yhPCcIInZXDyoDSv',
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
            '_route' => 'generated::HiXfDRjaCyCDt0Qw',
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
            '_route' => 'generated::ibDhH8h5RgAexhnT',
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
            '_route' => 'generated::vIGzfP15vL3JEw20',
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
            '_route' => 'generated::2gDhqVn2kFmK94aQ',
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
            '_route' => 'generated::IZBOmUQ7UJR4UsdX',
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
            '_route' => 'generated::i9gm27tiC89HAt9v',
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
            '_route' => 'generated::YKZPD85PuribZ2wZ',
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
            '_route' => 'generated::yXhfw9RqWEgd9BgR',
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
            '_route' => 'generated::iSRwRJeZEH5whDmR',
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
      '/api/v1/judge/judge/score-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::RMCMGJ40busrJ6xk',
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
            '_route' => 'generated::OHP0ju17Hi2orW4T',
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
            '_route' => 'generated::BYT2gutcIRvP5NbQ',
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
            '_route' => 'generated::eiOsJxJym42rbyFE',
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
            '_route' => 'generated::lubDZ21OrzmoDQw1',
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
            '_route' => 'generated::mcPrymE4A54atx6b',
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
            '_route' => 'generated::z4u0S65HjMLeYRID',
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
            '_route' => 'generated::2Zr3M2dbbvMsKe9U',
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
            '_route' => 'generated::FQwX4zUdBBLAVjAn',
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
            '_route' => 'generated::zR5nSGFHcepDPVgU',
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
            '_route' => 'generated::NRzlB8h7D9fVJkXF',
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
            '_route' => 'generated::gDiSeFq9VdlkkdSr',
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
            '_route' => 'generated::hIv05F68AG027j3Z',
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
            '_route' => 'generated::zcR9t1EagouppNvh',
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
            '_route' => 'generated::7Oi9WF0JmoKLQjE2',
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
            '_route' => 'generated::BsHWFwpQmFnqXBLS',
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
            '_route' => 'generated::booASgvdGWUlQXHo',
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
            '_route' => 'generated::axGb42xQn1SPMXaK',
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
            '_route' => 'generated::QUiIoJjwhWVTnBBl',
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
            '_route' => 'generated::E6jSU29iDJ9xoVnK',
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
            '_route' => 'generated::H5ZrgjzgYNBgAbPL',
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
            '_route' => 'generated::0LlzX8CTp0h5ingT',
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
            '_route' => 'generated::AG6i8g8IEI3UF9rF',
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
            '_route' => 'generated::omY5ENck0f5JGTaU',
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
            '_route' => 'generated::zfWSpfSD3lr7AFQS',
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
            '_route' => 'generated::0ocDRLweUvJ7UIZH',
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
            '_route' => 'generated::IeGWdthUzUpnRagE',
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
            '_route' => 'generated::K0Ugdurm0rbaNRSx',
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
            '_route' => 'generated::cG8njORjzuQt6V90',
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
            '_route' => 'generated::AgVF0Pz0MQ4KBijI',
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
            '_route' => 'generated::zr4Rgd43dScmczG0',
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
            '_route' => 'generated::gvfUlpAsqALgJxno',
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
            '_route' => 'generated::9rZH3s3VWjdabPBU',
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
            '_route' => 'generated::crFrD74CTA6q5VSE',
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
            '_route' => 'generated::KR0GSsnVBGM0R8YN',
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
            '_route' => 'generated::uXHXrqpftRisn7Mz',
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
            '_route' => 'generated::ZdFIYi6OlcQyIkjg',
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
            '_route' => 'generated::2NvbCCX4EuKTnA48',
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
            '_route' => 'generated::NqLq7BtAhTLOv0vZ',
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
            '_route' => 'generated::2XosTtL0thHvfjb0',
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
            '_route' => 'generated::IAJu4aUkS2U2v0W4',
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
            '_route' => 'generated::AXskCreB8LE8NQWs',
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
            '_route' => 'generated::W9earIpQ1hsDAPsz',
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
            '_route' => 'generated::hJIW0h0hUC6JwTs0',
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
            '_route' => 'generated::cRCPZqfwontDAbDb',
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
            '_route' => 'generated::qPtn5DlI2BJ9m9lT',
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
            '_route' => 'generated::BChRf1gy1yx9uhCJ',
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
            '_route' => 'generated::7h0t89zPiyR0wsFx',
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
            '_route' => 'generated::6qzm0Wo6yuQmSqfA',
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
    'generated::84aaGPvswjsoZlJk' => 
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
        'as' => 'generated::84aaGPvswjsoZlJk',
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
    'generated::F5ocOoDwCvWZEmqE' => 
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
        'as' => 'generated::F5ocOoDwCvWZEmqE',
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
    'generated::gs3PgRfj4LTfY63K' => 
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
        'as' => 'generated::gs3PgRfj4LTfY63K',
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
    'generated::5ImC7Eatvbl1x0IU' => 
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
        'as' => 'generated::5ImC7Eatvbl1x0IU',
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
    'generated::gX8i4Ug04KTpp3Xf' => 
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
        'as' => 'generated::gX8i4Ug04KTpp3Xf',
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
    'generated::CPqhwIRaRmyenTCG' => 
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
        'as' => 'generated::CPqhwIRaRmyenTCG',
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
    'generated::tawrUeWnJ2hlyQ3O' => 
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
        'as' => 'generated::tawrUeWnJ2hlyQ3O',
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
    'generated::yhPCcIInZXDyoDSv' => 
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
        'as' => 'generated::yhPCcIInZXDyoDSv',
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
    'generated::ibDhH8h5RgAexhnT' => 
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
        'as' => 'generated::ibDhH8h5RgAexhnT',
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
    'generated::HiXfDRjaCyCDt0Qw' => 
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
        'as' => 'generated::HiXfDRjaCyCDt0Qw',
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
    'generated::BYT2gutcIRvP5NbQ' => 
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
        'as' => 'generated::BYT2gutcIRvP5NbQ',
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
    'generated::eiOsJxJym42rbyFE' => 
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
        'as' => 'generated::eiOsJxJym42rbyFE',
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
    'generated::vIGzfP15vL3JEw20' => 
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
        'as' => 'generated::vIGzfP15vL3JEw20',
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
    'generated::2gDhqVn2kFmK94aQ' => 
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
        'as' => 'generated::2gDhqVn2kFmK94aQ',
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
    'generated::lubDZ21OrzmoDQw1' => 
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
        'as' => 'generated::lubDZ21OrzmoDQw1',
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
    'generated::mcPrymE4A54atx6b' => 
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
        'as' => 'generated::mcPrymE4A54atx6b',
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
    'generated::6qzm0Wo6yuQmSqfA' => 
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
        'as' => 'generated::6qzm0Wo6yuQmSqfA',
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
    'generated::z4u0S65HjMLeYRID' => 
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
        'as' => 'generated::z4u0S65HjMLeYRID',
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
    'generated::omY5ENck0f5JGTaU' => 
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
        'as' => 'generated::omY5ENck0f5JGTaU',
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
    'generated::zfWSpfSD3lr7AFQS' => 
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
        'as' => 'generated::zfWSpfSD3lr7AFQS',
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
    'generated::2Zr3M2dbbvMsKe9U' => 
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
        'as' => 'generated::2Zr3M2dbbvMsKe9U',
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
    'generated::FQwX4zUdBBLAVjAn' => 
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
        'as' => 'generated::FQwX4zUdBBLAVjAn',
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
    'generated::zR5nSGFHcepDPVgU' => 
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
        'as' => 'generated::zR5nSGFHcepDPVgU',
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
    'generated::NRzlB8h7D9fVJkXF' => 
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
        'as' => 'generated::NRzlB8h7D9fVJkXF',
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
    'generated::axGb42xQn1SPMXaK' => 
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
        'as' => 'generated::axGb42xQn1SPMXaK',
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
    'generated::gDiSeFq9VdlkkdSr' => 
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
        'as' => 'generated::gDiSeFq9VdlkkdSr',
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
    'generated::zcR9t1EagouppNvh' => 
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
        'as' => 'generated::zcR9t1EagouppNvh',
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
    'generated::7Oi9WF0JmoKLQjE2' => 
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
        'as' => 'generated::7Oi9WF0JmoKLQjE2',
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
    'generated::hIv05F68AG027j3Z' => 
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
        'as' => 'generated::hIv05F68AG027j3Z',
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
    'generated::BsHWFwpQmFnqXBLS' => 
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
        'as' => 'generated::BsHWFwpQmFnqXBLS',
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
    'generated::booASgvdGWUlQXHo' => 
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
        'as' => 'generated::booASgvdGWUlQXHo',
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
    'generated::K0Ugdurm0rbaNRSx' => 
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
        'as' => 'generated::K0Ugdurm0rbaNRSx',
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
    'generated::2NvbCCX4EuKTnA48' => 
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
        'as' => 'generated::2NvbCCX4EuKTnA48',
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
    'generated::cG8njORjzuQt6V90' => 
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
        'as' => 'generated::cG8njORjzuQt6V90',
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
    'generated::AgVF0Pz0MQ4KBijI' => 
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
        'as' => 'generated::AgVF0Pz0MQ4KBijI',
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
    'generated::zr4Rgd43dScmczG0' => 
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
        'as' => 'generated::zr4Rgd43dScmczG0',
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
    'generated::ZdFIYi6OlcQyIkjg' => 
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
        'as' => 'generated::ZdFIYi6OlcQyIkjg',
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
    'generated::gvfUlpAsqALgJxno' => 
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
        'as' => 'generated::gvfUlpAsqALgJxno',
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
    'generated::crFrD74CTA6q5VSE' => 
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
        'as' => 'generated::crFrD74CTA6q5VSE',
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
    'generated::KR0GSsnVBGM0R8YN' => 
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
        'as' => 'generated::KR0GSsnVBGM0R8YN',
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
    'generated::9rZH3s3VWjdabPBU' => 
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
        'as' => 'generated::9rZH3s3VWjdabPBU',
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
    'generated::uXHXrqpftRisn7Mz' => 
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
        'as' => 'generated::uXHXrqpftRisn7Mz',
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
    'generated::NqLq7BtAhTLOv0vZ' => 
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
        'as' => 'generated::NqLq7BtAhTLOv0vZ',
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
    'generated::2XosTtL0thHvfjb0' => 
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
        'as' => 'generated::2XosTtL0thHvfjb0',
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
    'generated::IAJu4aUkS2U2v0W4' => 
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
        'as' => 'generated::IAJu4aUkS2U2v0W4',
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
    'generated::AXskCreB8LE8NQWs' => 
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
        'as' => 'generated::AXskCreB8LE8NQWs',
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
    'generated::W9earIpQ1hsDAPsz' => 
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
        'as' => 'generated::W9earIpQ1hsDAPsz',
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
    'generated::hJIW0h0hUC6JwTs0' => 
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
        'as' => 'generated::hJIW0h0hUC6JwTs0',
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
    'generated::cRCPZqfwontDAbDb' => 
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
        'as' => 'generated::cRCPZqfwontDAbDb',
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
    'generated::qPtn5DlI2BJ9m9lT' => 
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
        'as' => 'generated::qPtn5DlI2BJ9m9lT',
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
    'generated::BChRf1gy1yx9uhCJ' => 
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
        'as' => 'generated::BChRf1gy1yx9uhCJ',
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
    'generated::7h0t89zPiyR0wsFx' => 
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
        'as' => 'generated::7h0t89zPiyR0wsFx',
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
    'generated::QUiIoJjwhWVTnBBl' => 
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
        'as' => 'generated::QUiIoJjwhWVTnBBl',
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
    'generated::E6jSU29iDJ9xoVnK' => 
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
        'as' => 'generated::E6jSU29iDJ9xoVnK',
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
    'generated::H5ZrgjzgYNBgAbPL' => 
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
        'as' => 'generated::H5ZrgjzgYNBgAbPL',
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
    'generated::0LlzX8CTp0h5ingT' => 
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
        'as' => 'generated::0LlzX8CTp0h5ingT',
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
    'generated::AG6i8g8IEI3UF9rF' => 
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
        'as' => 'generated::AG6i8g8IEI3UF9rF',
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
    'generated::IeGWdthUzUpnRagE' => 
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
        'as' => 'generated::IeGWdthUzUpnRagE',
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
    'generated::0ocDRLweUvJ7UIZH' => 
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
        'as' => 'generated::0ocDRLweUvJ7UIZH',
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
    'generated::IZBOmUQ7UJR4UsdX' => 
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
        'as' => 'generated::IZBOmUQ7UJR4UsdX',
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
    'generated::i9gm27tiC89HAt9v' => 
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
        'as' => 'generated::i9gm27tiC89HAt9v',
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
    'generated::YKZPD85PuribZ2wZ' => 
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
        'as' => 'generated::YKZPD85PuribZ2wZ',
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
    'generated::yXhfw9RqWEgd9BgR' => 
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
        'as' => 'generated::yXhfw9RqWEgd9BgR',
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
    'generated::iSRwRJeZEH5whDmR' => 
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
        'as' => 'generated::iSRwRJeZEH5whDmR',
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
    'generated::RMCMGJ40busrJ6xk' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/judge/judge/score-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
          2 => 'role:judge',
          3 => 'no.cache',
        ),
        'uses' => 'App\\Http\\Controllers\\JudgeController@scoreStatus',
        'controller' => 'App\\Http\\Controllers\\JudgeController@scoreStatus',
        'namespace' => NULL,
        'prefix' => 'api/v1/judge',
        'where' => 
        array (
        ),
        'as' => 'generated::RMCMGJ40busrJ6xk',
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
    'generated::OHP0ju17Hi2orW4T' => 
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
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000005ca0000000000000000";}}',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::OHP0ju17Hi2orW4T',
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
