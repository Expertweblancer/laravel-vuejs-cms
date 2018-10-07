<?php

return array(
    // 'dsn' => 'https://5087c823d6554286a0b7c3399077d1e2:8b81a6b149114e38b8b3d44261bd9a95@sentry.io/296482',
    'dsn' => '',

    // capture release as git sha
    // 'release' => trim(exec('git log --pretty="%h" -n1 HEAD')),

    // Capture bindings on SQL queries
    'breadcrumbs.sql_bindings' => true,

    // Capture default user context
    'user_context' => true,
);
