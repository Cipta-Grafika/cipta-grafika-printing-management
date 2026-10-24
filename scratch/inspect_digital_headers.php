<?php

$excelPath = 'C:/Users/faisal/.gemini/antigravity/brain/638ea06c-a260-462d-a201-b9a162e0b2b8/.user_uploaded/media_1792760410164_cc936f91.xlsx';

$zip = new ZipArchive();
$zip->open($excelPath);

$sheetXml = $zip->getFromName('xl/worksheets/sheet3.xml');
$sheet = simplexml_load_string($sheetXml);

$strings = [];
$ssXml = $zip->getFromName('xl/sharedStrings.xml');
if ($ssXml) {
    $ss = simplexml_load_string($ssXml);
    foreach ($ss->si as $si) {
        $strings[] = isset($si->t) ? (string)$si->t : (isset($si->r) ? implode('', array_map(fn($r)=>(string)$r->t, iterator_to_array($si->r))) : '');
    }
}

$rows = [];
foreach ($sheet->sheetData->row as $row) {
    $rNum = (int)$row['r'];
    if ($rNum >= 4 && $rNum <= 13) {
        foreach ($row->c as $c) {
            $ref = (string)$c['r'];
            $type = (string)$c['t'];
            $val = (string)$c->v;
            if ($type === 's') $val = $strings[(int)$val] ?? '';
            preg_match('/^([A-Z]+)(\d+)$/', $ref, $m);
            $rows[$rNum][$m[1]] = $val;
        }
    }
}

$cols = ['Y','Z','AA','AB','AC','AD','AE','AF'];
echo "COL | Row 4 | Row 5 | Row 6 | Row 7 | Row 8 (AP150 A5) | Row 9 (AP120 A5)\n";
echo str_repeat('-', 90) . "\n";
foreach ($cols as $c) {
    printf("%-3s | %-16s | %-12s | %-8s | %-12s | %-10s | %-10s\n",
        $c,
        $rows[4][$c] ?? '',
        $rows[5][$c] ?? '',
        $rows[6][$c] ?? '',
        $rows[7][$c] ?? '',
        $rows[8][$c] ?? '',
        $rows[9][$c] ?? ''
    );
}

$zip->close();

