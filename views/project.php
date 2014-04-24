<?php
namespace Webdashboard;

$body_class = $body_class . ' project';
$links = '<script language="javascript" type="text/javascript" src="./assets/js/sorttable.js"></script>'
       . '<script language="javascript" type="text/javascript" src="./assets/js/toggle.js"></script>';
$content = "<button class=\"button\" onclick=\"toggle('localedone')\">Toggle completed locales</button>
    <table class=\"table sortable\" id=\"project\">
        <caption>L10n Project Dashboard ($project)</caption>
        <thead>
            <tr>
                <th>Locale</th>";

// Display columns name
foreach ($pages as $page => $page_status) {
    $content .= '<td>' . $page . '</td>';
}
$content .= '
            </tr>
        </thead>
        <tbody>';

// Display status for all pages per locale
foreach ($status as $locale => $array_status) {
    $working_on_locamotion = in_array($locale, $locamotion);
    $done = array_key_exists($locale, array_flip($locale_done));
    if ($done) {
        $content .= '<tr class="localedone">' . "\n";
    } else {
        $content .= '<tr>' . "\n";
    }
    $content .= "<td><a href=\"?locale=$locale\">$locale";
    if ($working_on_locamotion) {
        $content .= '<img src="./assets/images/locamotion_16.png" class="locamotion" />';
    }
    $content .= '</a></td>' . "\n";
    foreach ($array_status as $key => $result) {
        $cell = $class = '';

        // This locale does not have this page
        if ($result == 'none') {
            $cell = '1';
            $class = $result;
        } else {
            // Page done
            if ($result  == 'done') {
                $cell = '100%';
                $class = $result;
            // Missing
            } elseif ($result == 'missing') {
                $cell = '0%';
                $class = $result;
            // In progress
            } else {
                $cell = $result;
                $class = 'inprogress';
            }
        }
        $content .= '<td class="' . $class . '">' . $cell . '</td>' . "\n";
    }
    $content .= '</tr>' . "\n";
}
$content .= '</tbody>'
          . '</table>';

// Display stats per page
$content .= '<table class="results sortable">
                <thead>
                  <tr>
                    <th>Page</th><th>Completion</th><th>% of ADU</th>
                  </tr>
                </thead>
                <tbody>';

foreach ($pages as $page => $page_status) {
    $done = count($page_status['done_locales']);
    $nb_locales = $page_status['nb_locales'];

    $content .= '<tr><td colspan="1" class="left">' . $page . '</td><td colspan="1"';
    // Page done for all target locales
    if ($done == $nb_locales) {
        $content .= ' class="green"';
    }
    $content .= '> ' . $done . ' locales ready on ' . $nb_locales  . '</td>'
              . '<td colspan="1">' . $page_status['coverage'] . '%</td></tr>';
}

// Display global stats
$content .= '<tr><td colspan="3" class="final">Total: ' . count($locale_done) . ' locales ready (' . $perfect_locales_coverage . '% of ADU)</td></tr>'
          . '<tr><td colspan="3">On average, we have ' . $average_nb_locales . ' locales ready per file (' . $average_coverage . '% of ADU)</td></tr>'
          . '</tbody>'
          . '</table>';

include __DIR__ . '/../templates/' . $template;
