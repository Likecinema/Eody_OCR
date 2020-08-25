<?php
error_reporting(0);
require('./vendor/autoload.php');
use thiagoalessio\TesseractOCR\TesseractOCR;
chdir('./daily_reports/');
$folders = preg_grep('/.[a-zA-Z0-9?]+_images/', scandir('./'));
//Usage: Given an array with .folder-name-example names, and within the folder are folder-name-example-$i.jpg
//types of images, where $i is a number, ocr the pages and get date/cases with regex inside
function echo_csv($folders, $header)
{
    $date_regex = '/[0-9]{1,2} (Ιανουαρίου|Φεβρουαρίου|Μαρτίου|Απριλίου|Μαΐου|Ιουνίου|Ιουλίου|Αυγούστου|Σεπτεμβρίου|Οκτωβρίου|Νοεμβρίου|Δεκεμβρίου|January|February|March|April|May|June|July|August|September|October|November|December) (202[0-9])*/m';
    $new_cases_regex = '/(α (νέα επιβεβαιωμένα εργαστηριακά |εργαστηριακά επιβεβαιωμένα |νέα εργαστηριακά επιβεβαιωμένα )κρούσματα της νόσου[ \n\r]*(αυξήθηκαν κατά |είναι)([ 0-9]+)(.|)|((εκτων|εΚτων|εκ των) οποίων ([0-9]+) (το τελευταίο|νέα)))/';
    if ($header == true) {
        echo '"day","new cases"';
    }
    foreach ($folders as $folder) {
        $text = new TesseractOCR("./" . $folder . "/" . substr($folder, 1, -7) . "-0.jpg");
        $text->lang('ell', 'eng');
        $run = $text->run();
        $run = str_replace('Λ', 'Α', $run);
        $run = str_replace('ἶ', 'ί', $run);
        $run = str_replace('Ί', '1', $run);
        $run = str_replace('ἁ', 'ά', $run);
        $run = str_replace('ῆ', 'η', $run);
        $run = str_replace('µ', 'μ', $run);
        $run = str_replace('ἰ', 'ί', $run);
        $run = str_replace('ἐ', 'έ', $run);
        $run = str_replace('ἐ', 'έ', $run);
        preg_match($date_regex, $run, $date);
        preg_match($new_cases_regex, $run, $new_cases);
        $new_cases = array_values(array_filter($new_cases));
        if (sizeof($new_cases) != 0) {
            echo $date[0] . ',' . $new_cases[sizeof($new_cases) - 2] . PHP_EOL;
        } else {
            echo ($date[0] . ',NaN');
        }
    }
}
