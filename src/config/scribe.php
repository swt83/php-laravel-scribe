<?php

return array(

    #################################################
    # Location of content files.
    #################################################
    'directory' => app_path().'/views/scribe/',

    #################################################
    # Number of minutes before a content refresh.
    #################################################
    'refresh' => 1,

    #################################################
    # Fields that should be considered as array.
    #################################################
    'fields_as_array' => array(
        'category',
        'tag',
    )

);