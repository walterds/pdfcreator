<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'src/import_csv.php';
require_once 'src/generate_pdf.php'; // FPDF is required within this file

// Configuration
$csvFile = 'data/games.csv';
$outputPdfFile = 'output/game_catalog.pdf';

echo "Script starting...\n";

// 1. Import data from CSV
echo "Importing data from $csvFile...\n";
$gameData = import_csv($csvFile);

if (empty($gameData)) {
    echo "No data imported or CSV file is empty. Exiting.\n";
    // You might want to create an empty PDF or handle this differently
    // For now, just exit.
    // Create an empty PDF to indicate the process ran but had no data.
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,10,'No data found in CSV.',0,1,'C');
    $pdf->Output('F', $outputPdfFile);
    echo "Empty PDF generated at $outputPdfFile as no data was found.\n";
    exit;
}
echo count($gameData) . " records imported.\n";

// 2. Generate PDF
echo "Generating PDF...\n";
try {
    generate_pdf($gameData, $outputPdfFile);
    echo "PDF generated successfully: $outputPdfFile\n";
} catch (Exception $e) {
    echo "Error generating PDF: " . $e->getMessage() . "\n";
    // Log the full error stack trace if possible or needed
    error_log("PDF Generation Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
}

echo "Script finished.\n";

?>
