<?php
require('./vendor/autoload.php');
use PHPHtmlParser\Dom;
function fetch_pdf_links($link, $oneday = NULL)
{
    $pdfs = array();
    $regex = '/[0-9]{1,2}[ \/](Ιανουαρίου|Φεβρουαρίου|Μαρτίου|Απριλίου|Μαΐου|Ιουνίου|Ιουλίου|Αυγούστου|Σεπτεμβρίου|Οκτωβρίου|Νοεμβρίου|Δεκεμβρίου|[0,9]{1,2})([ \/]202[0-9])*/m';
    $dom = new Dom;
    $dom->loadFromUrl($link);
    $html = $dom->outerHtml;
    $a = $dom->find('a');
    foreach ($a as $ahref){
            if (preg_match_all($regex, $ahref->text, $matches, PREG_SET_ORDER, 0)) {
            array_push($pdfs, $ahref->href);
        }
    }
    if($oneday == NULL){
    return $pdfs;
    }
    else return Array($pdfs[$oneday]);
}
function endsWith($haystack, $needle){
    $length = strlen($needle);
    if (!$length){
        return false;
    }
    return (substr($haystack, -$length) === $needle);
}
function is_pdf($link)
{
    $type = substr(file_get_contents($link), 0, 4);
    if (preg_match_all('/%PDF/', $type)) {
        return true;
    } else return false;
}
function download_pdf_report($daily_report)
{
    if (is_pdf($daily_report)) {
        $file_name = basename($daily_report);
        if (!file_exists($file_name)) {
            if (file_put_contents($file_name, file_get_contents($daily_report))) {
                echo "File " . $file_name . " from " . $daily_report . " downloaded successfully" . PHP_EOL;
            } else {
                echo "File " . $file_name . " from " . $daily_report . "  downloading failed." . PHP_EOL;
            }
        }
    } else {
        $corrected_pdf = fetch_pdf_links($daily_report);
        print_r($corrected_pdf);
        foreach ($corrected_pdf as $pdf) {
            if (is_pdf($pdf) && !endsWith($pdf, '.pdf'))
                download_pdf_report($pdf);
        }
    }
}
mkdir('./daily_reports');
$links = fetch_pdf_links('https://eody.gov.gr/epidimiologika-statistika-dedomena/ektheseis-covid-19/');
$today = fetch_pdf_links('https://eody.gov.gr/epidimiologika-statistika-dedomena/ektheseis-covid-19/', 0);
#print_r($today);
chdir('./daily_reports');
print_r($links);
foreach ($links as $daily_report) {
   download_pdf_report($daily_report);
}

