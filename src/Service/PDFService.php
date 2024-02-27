<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PDFService
{
    private $domPDF;
    public function __construct()
    {
        $this->domPDF= new Dompdf();
        $pdfOptions=new Options();
        $pdfOptions->set('defaultFont', 'Garamond');
        $pdfOptions->set('debug', true);
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->set('isPhpEnabled', true);
        $pdfOptions->set('isRemoteEnabled', true);
        $pdfOptions->set('chroot',realpath(''));
        $pdfOptions->set('defaultPaperSize', 'A4');
        $pdfOptions->set('defaultPaperOrientation', 'landscape');
        $this->domPDF->setOptions($pdfOptions);
    }

    public function showPDF($html,$namepdf)
    {
        $this->domPDF->loadHtml($html, 'UTF-8');
        $this->domPDF->render();
        $this->domPDF->stream($namepdf,[
            'Attachement'=>false
        ]);
    }

    public function generatePDF($html)
    {
        $this->domPDF->loadHtml($html);
        $this->domPDF->render();
        return $this->domPDF->output();

    }
}