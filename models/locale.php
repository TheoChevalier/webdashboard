<?php
/*
 * Model for individual locale view
 */
namespace Webdashboard;

$json   = isset($_GET['json']) ? true : false;
$locale = $_GET['locale'];

// include all data about our locales
include __DIR__ . '/../data/locales.php';

// Check that this is a valid locale code called via GET
if (!isset($_GET['locale']) || !in_array($_GET['locale'], $locales)) {
    $content = '<h2>Wrong locale code</h2>';
    include __DIR__ . '/../views/error.php';
    return;
} else {
    $locale = $_GET['locale'];
}

// get lang files status from langchecker
$lang_files = Json::fetch(LANG_CHECKER . "?locale={$locale}&json");

// check if the locale is working on locamotion
$locamotion = Json::fetch(Utils::cacheUrl(LANG_CHECKER . '?action=locamotion&json', 15*60));
$locamotion = (in_array($locale, $locamotion));

// all open bugs for a locale in the mozilla.org/l10n component
$bugzilla_query_mozillaorg = 'https://bugzilla.mozilla.org/buglist.cgi?'
                           . 'f1=cf_locale'
                           . '&o1=equals'
                           . '&query_format=advanced'
                           . '&v1=' . urlencode(Utils::getBugzillaLocaleField($locale))
                           . '&o2=equals'
                           . '&f2=component'
                           . '&v2=L10N'
                           . '&bug_status=UNCONFIRMED'
                           . '&bug_status=NEW'
                           . '&bug_status=ASSIGNED'
                           . '&bug_status=REOPENED'
                           . '&classification=Other'
                           . '&product=www.mozilla.org';

// all open bugs for a locale in the Mozilla Localization/locale component, with "webdashboard" in the whiteboard
$bugzilla_query_l10ncomponent = 'https://bugzilla.mozilla.org/buglist.cgi?'
                              . '&query_format=advanced'
                              . '&status_whiteboard_type=allwordssubstr'
                              . '&status_whiteboard=webdashboard'
                              . '&bug_status=UNCONFIRMED'
                              . '&bug_status=NEW'
                              . '&bug_status=ASSIGNED'
                              . '&bug_status=REOPENED'
                              . '&component=' . urlencode(Utils::getBugzillaLocaleField($locale))
                              . '&classification=Client%20Software'
                              . '&product=Mozilla%20Localizations';


// cache in a local cache folder if possible
$csv_mozillaorg = Utils::cacheUrl($bugzilla_query_mozillaorg . '&ctype=csv', 15*60);
$csv_l10ncomponent = Utils::cacheUrl($bugzilla_query_l10ncomponent . '&ctype=csv', 15*60);

// generate all the bugs
$bugs_mozillaorg = Utils::getBugsFromCSV($csv_mozillaorg);
$bugs_l10ncomponent = Utils::getBugsFromCSV($csv_l10ncomponent);

$bugs = $bugs_mozillaorg + $bugs_l10ncomponent;

$rss_data = [];

if (count($bugs) > 0) {
    foreach ($bugs as $k => $v) {
        $rss_data[] = [$k, "https://bugzilla.mozilla.org/show_bug.cgi?id={$k}", $v];
    }
}

// Read status of external web projects, cache cleaned every hour.
$webprojects = Json::fetch(Utils::cacheUrl(WEBPROJECTS_JSON, 60*60));

// d($lang_files);
// RSS feed  data
$total_missing_strings = 0;
$link = LANG_CHECKER .'?locale=' . $locale;

foreach ($lang_files as $site => $tablo) {
    foreach ($tablo as $file => $details) {
        $count = $details['identical'] + $details['missing'];
        if ($count > 0) {
            $message = "You have $count strings untranslated in $file";
            $status = (isset($details['critical']) && $details['critical'])
                      ? 'Priority file'
                      : 'Nice to have';
            if (isset($details['deadline']) && $details['deadline']) {
              $deadline = date('F d', (new \DateTime($details['deadline']))->getTimestamp());
              $status .= ' (Deadline is ' . $deadline . ')';
            }
            $rss_data[] = array($status, $link, $message);
            $total_missing_strings += $count;
        }
    }
}

if ($total_missing_strings >0) {
    array_unshift(
        $rss_data,
        ['Total', $link, "You need to translate $total_missing_strings strings."]
    );
}

// Prepare a RSS feed
$rss = new Feed($rss_data);
$rss->title = "L10n Web Dashboard - ".$locale;
$rss->site  = "http://l10n.mozilla-community.org/webdashboard/?locale=".$locale;

include __DIR__ . '/../views/locale.php';
