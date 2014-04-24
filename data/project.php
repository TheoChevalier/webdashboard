<?php

// This is the list of the tracked pages
$australis_core = [
    'firefox/desktop/index.lang' => ['site' => 0],
    'firefox/desktop/fast.lang' => ['site' => 0],
    'firefox/desktop/trust.lang' => ['site' => 0],
    'firefox/desktop/customize.lang' => ['site' => 0],
    'firefox/australis/firefox_tour.lang' => ['site' => 0],
    'firefox/sync.lang' => ['site' => 0]
];

$australis_mozorg = $australis_core;
$australis_mozorg['firefox/new.lang'] = ['site' => 0];
$australis_mozorg['mozorg/home.lang'] = ['site' => 0];
$australis_mozorg['tabzilla/tabzilla.lang'] = ['site' => 0];

$australis_all = $australis_mozorg;
$australis_all['apr2014.lang'] = ['site' => 6];
$australis_all['main.lang'] = ['site' => 0];

$pages = [
    'default' => [
        'main.lang' => ['site' => 0]
    ],
    'australis_core' => $australis_core,
    'australis_mozorg' => $australis_mozorg,
    'australis_all' => $australis_all,
    'firefox_os' => [
        ['file' => 'firefox/os/index.lang',
         'site' => 0],
        ['file' => 'firefox/os/faq.lang',
         'site' => 0],
        ['file' => 'firefox/partners/index.lang',
         'site' => 0],
    ],
    'firefox_usage' => [
        ['file' => 'firefox/desktop/tips.lang',
         'site' => 0],
    ],
];
