<?php

/*
|--------------------------------------------------------------------------
| SPRS navigation
|--------------------------------------------------------------------------
|
| One list, filtered by the signed-in user's hierarchy level. Separate
| sidebar files per role drift apart — someone adds an item to FO's and
| forgets RO's.
|
| 'levels' is which hierarchy_level values see the item:
|   1 JPO   2 PESO City/Municipality   3 PESO Province   4 FO   5 RO
|
| 'roles' matches users.role, for accounts outside the hierarchy.
|
| 'icon' names an entry in the $icons map at the top of
| resources/views/sprs/partials/sidebar.blade.php.
|
| Items whose route doesn't exist yet are skipped rather than throwing, so
| this file can be filled in ahead of the pages.
|
*/

return [

    'groups' => [
        [
            'label' => 'Module 1',
            'items' => [
                 [
                    'label'  => 'Dashboard',
                    'route'  => 'sprs.dashboard',
                    'icon'   => 'chart',
                    'levels' => [1, 2, 3, 4, 5],
                ],

                [
                    // The office's own figures. FO and RO don't file, so a
                    // link here would only ever open their own empty form —
                    // they reach an office's report through the compilation.
                    'label'  => 'My Accomplishment',
                    'route'  => 'sprs.accomplishment',
                    'icon'   => 'form',
                    'levels' => [1, 2, 3],
                ],

                [
                    // Own report plus everything gathered from below, and the
                    // button that passes the batch upward. A JPO has nothing
                    // beneath it, so nothing to compile.
                    'label'  => 'Compilation',
                    'route'  => 'sprs.compilation',
                    'icon'   => 'list',
                    'levels' => [2, 3, 4],
                ],

                [
                    // Same page, different word. RO reviews a region rather
                    // than compiling one.
                    'label'  => 'Submissions',
                    'route'  => 'sprs.compilation',
                    'icon'   => 'list',
                    'levels' => [5],
                ],
            ],
        ],

        [
            'label' => 'Administration',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'sprs.superadmin.dashboard',
                    'icon'  => 'chart',
                    'roles' => ['super_admin'],
                ],
                [
                    'label' => 'Create User Account',
                    'route' => 'sprs.superadmin.create_account',
                    'icon'  => 'user',
                    'roles' => ['super_admin'],
                ],
            ],
        ],
    ],

];