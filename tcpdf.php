<?php
require_once __DIR__ . '/tcpdf/tcpdf.php';

// Extend TCPDF to create a custom header and footer
class MYPDF extends TCPDF {
    // Custom Header
    public function Header() {
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 10, 'Gate Pass Reports', 0, 1, 'C');
        $this->Ln(5);
    }

    // Custom Footer
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}
?>
