<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\wkhtmltopdf;

class Main {
  /*
   * @param $returntype: 'file' will return a filename from temporary directory, you should then erase it when done
   *                     'content' will return the pdf content
   */
  public static function htmltopdf($html, $returntype = 'file', $options='') {
   	$bin=dirname(__FILE__).'/bin/wkhtmltopdf-amd64';
    $htmlfilename=tempnam('/tmp', 'htmltopdf-html-').'.html';
    $pdffilename=tempnam('/tmp', 'htmltopdf-pdf-').'.pdf';
    file_put_contents($htmlfilename, $html);

    exec($bin.' '.$options.' '.$htmlfilename.' '.$pdffilename);
    
    if ($returntype == 'file') return $pdffilename;
    else if ($returntype == 'content') {
      $res=file_get_contents($pdffilename);
      unlink($pdffilename);
      return $res;
    }
  }
}
