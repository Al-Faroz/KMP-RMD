<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { color: #202020; font-family: dejavusans, sans-serif; font-size: 9pt; }
        h1 { margin: 4mm 0; font-size: 14pt; text-align: center; }
        .kop { width: 100%; text-align: center; border-bottom: 1.4pt double #333; }
        .kop td { padding-bottom: 1mm; }
        .kop-logo { width: 25mm; }
        .kop-madrasah { font-family: dejavuserif, serif; font-size: 13pt; font-weight: bold; }
        .kop-alamat { font-family: dejavuserif, serif; font-style: italic; }
        table { border-collapse: collapse; }
        .isian { width: 100%; }
        .isian td { padding: 1mm 1.2mm; vertical-align: top; }
        .label { width: 37%; }
        .sub { padding-left: 6mm !important; }
        .ttd { width: 100%; margin-top: 4mm; page-break-inside: avoid; }
        .ttd td { vertical-align: top; }
        .ttd-nama { padding-top: 13mm; font-weight: bold; }
    </style>
    <?= $this->renderSection('style') ?>
</head>
<body>
    <?= $this->renderSection('isi') ?>
</body>
</html>
