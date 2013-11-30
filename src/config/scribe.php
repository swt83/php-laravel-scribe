<?php

return array(

    #################################################
    # Location of files.
    #################################################
    'directory' => app_path().'/views/scribe/', // ending slash is important

    #################################################
    # Chances of master record recompile.
    #################################################
    'lottery' => array(2, 100),

    #################################################
    # Chances of cache reset.
    #################################################
    'mode' => 'markdown',

);