<?php
chdir('./daily_reports');
$files = preg_grep('/^([^.])/', scandir('./'));

//Usage: Given an array with PDF file titles, make a hidden folder that has the name of the file
//and create a jpg for each page inside the folder
function pdf_array_to_images($files)
{
    $maxsize = 5000;
    foreach ($files as $file) {
        mkdir('.' . $file . "_images");
        chdir('.' . $file . "_images");
        $imagick = new Imagick();
        echo date("h:i:sa") . ' : Making images out of ' . $file . '...' . PHP_EOL;
        $imagick->setResolution(384, 512);
        $imagick->readImage("../" . $file);
        $imagick->setImageFormat("jpg");
        $imagick->setImageCompression(imagick::COMPRESSION_JPEG);
        $imagick->setImageCompressionQuality(100);
        foreach ($imagick as $c => $_page) {
            $_page->setImageBackgroundColor('white');
            $_page->adaptiveResizeImage($maxsize, $maxsize, true);
            $blankPage = new \Imagick();
            $blankPage->newPseudoImage($_page->getImageWidth(), $_page->getImageHeight(), "canvas:white");
            $blankPage->compositeImage($_page, \Imagick::COMPOSITE_ATOP, 0, 0);
            $blankPage->writeImage("$file-$c.jpg");
            $blankPage->clear();
        }
        chdir('..');
        $imagick->clear();
        echo date("h:i:sa") . ' : Done.' . PHP_EOL;
    }
}
?>