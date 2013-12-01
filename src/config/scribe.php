<?php

return array(

    #################################################
    # Location of content files.
    #################################################
    'directory' => app_path().'/views/scribe/',

    #################################################
    # Number of minutes before a cache check.
    #################################################
    'refresh' => isset($_SERVER['LARAVEL_ENV']) ? 10 : 0,

    #################################################
    # Field values that should be treated as arrays.
    #################################################
    'splits' => array(
        'category',
        'tag',
    )

);