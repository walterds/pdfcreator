<?php
require('fpdf.php');

class PDF extends FPDF
{
    // Page header
    function Header()
    {
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        // Move to the right
        $this->Cell(80);
        // Title
        $this->Cell(30,10,'Game Catalog',0,0,'C');
        // Line break
        $this->Ln(20);
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }

    // Function to add a "Destacado" type slide
    function addDestacadoSlide(array $item)
    {
        $this->AddPage();
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10,$item['Title'],0,1,'C');
        $this->Ln(5);

        // Placeholder for image
        $imagePath = $item['Image_location'];
        $imageWidth = 80;
        if (!empty($imagePath) && file_exists($imagePath) && filesize($imagePath) > 0 && in_array(strtolower(pathinfo($imagePath, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])) {
            // Adjust X, Y, Width, Height as needed
            // Center image: (210 - width) / 2 for A4 portrait
            $this->Image($imagePath, (210 - $imageWidth) / 2, $this->GetY(), $imageWidth);
            $this->Ln($imageWidth + 5); // Adjust spacing after image
        } else {
            $this->SetFont('Arial','',12);
            $this->Cell(0,10,'(Image not available: ' . basename($imagePath) . ')',0,1,'C');
            $this->Ln(10);
        }

        $this->SetFont('Arial','',12);
        $this->MultiCell(0,10,'Publisher: ' . $item['Publisher']);
        $this->MultiCell(0,10,'PEGI: ' . $item['Pegi']);
        $this->MultiCell(0,10,'Description: ' . $item['Description']);
        $this->MultiCell(0,10,'Controllers: ' . $item['Controllers']);
    }

    // Function to add a "New" type slide (two entries)
    function addNewSlide(array $item1, ?array $item2 = null)
    {
        $this->AddPage();
        $this->SetFont('Arial','B',14);

        // Entry 1
        $this->Cell(0,10,$item1['Title'],0,1,'C');
        $this->Ln(2);
        $imagePath1 = $item1['Image_location'];
        $imageWidth = 60;
        if (!empty($imagePath1) && file_exists($imagePath1) && filesize($imagePath1) > 0 && in_array(strtolower(pathinfo($imagePath1, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])) {
            $this->Image($imagePath1, (210 - $imageWidth) / 2, $this->GetY(), $imageWidth);
             $this->Ln($imageWidth + 2);
        } else {
            $this->SetFont('Arial','',10);
            $this->Cell(0,10,'(Image 1 not available: ' . basename($imagePath1) . ')',0,1,'C');
            $this->Ln(5);
        }
        $this->SetFont('Arial','',10);
        $this->MultiCell(0,7,'Publisher: ' . $item1['Publisher']);
        $this->MultiCell(0,7,'PEGI: ' . $item1['Pegi']);
        // Truncate long descriptions for "New" type if necessary
        $desc1 = strlen($item1['Description']) > 100 ? substr($item1['Description'],0,97).'...' : $item1['Description'];
        $this->MultiCell(0,7,'Description: ' . $desc1);
        $this->MultiCell(0,7,'Controllers: ' . $item1['Controllers']);
        $this->Ln(5);


        if ($item2) {
            $this->Line(10, $this->GetY(), 200, $this->GetY()); // Separator line
            $this->Ln(5);
            $this->SetFont('Arial','B',14);
            $this->Cell(0,10,$item2['Title'],0,1,'C');
            $this->Ln(2);
            $imagePath2 = $item2['Image_location'];
            // $imageWidth is already 60 from item1
            if (!empty($imagePath2) && file_exists($imagePath2) && filesize($imagePath2) > 0 && in_array(strtolower(pathinfo($imagePath2, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])) {
                 $this->Image($imagePath2, (210 - $imageWidth) / 2, $this->GetY(), $imageWidth);
                 $this->Ln($imageWidth + 2);
            } else {
                $this->SetFont('Arial','',10);
                $this->Cell(0,10,'(Image 2 not available: ' . basename($imagePath2) . ')',0,1,'C');
                $this->Ln(5);
            }
            $this->SetFont('Arial','',10);
            $this->MultiCell(0,7,'Publisher: ' . $item2['Publisher']);
            $this->MultiCell(0,7,'PEGI: ' . $item2['Pegi']);
            $desc2 = strlen($item2['Description']) > 100 ? substr($item2['Description'],0,97).'...' : $item2['Description'];
            $this->MultiCell(0,7,'Description: ' . $desc2);
            $this->MultiCell(0,7,'Controllers: ' . $item2['Controllers']);
        }
    }

    // Function to add a "Genre" type slide (four entries)
    function addGenreSlide(array $items, string $genre)
    {
        $this->AddPage();
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10, $genre . ' Games',0,1,'C');
        $this->Ln(5);

        $itemCount = 0;
        $cellWidth = 90; // Width for each item's cell (A4 width is 210, using 2 columns with margin)
        $xPos = [10, 110]; // X positions for the two columns
        $currentX = 0; // 0 for left, 1 for right column

        foreach ($items as $item) {
            if ($itemCount % 2 == 0 && $itemCount > 0) { // After every 2 items, check if new row needed
                 // If we filled 2 items and there are more, create space.
                 // This logic might need adjustment based on content height.
                 // For simplicity, assuming fixed height or manual page breaks if content overflows.
            }
            if ($itemCount == 2) { // After 2 items, move to a "new line" below them
                $this->Ln(65); // Approximate height for two items + image. Adjust as needed.
            }


            $startY = $this->GetY();
            $this->SetX($xPos[$currentX]);

            $this->SetFont('Arial','B',10);
            // Use MultiCell for title to allow wrapping if too long
            $this->MultiCell($cellWidth, 6, $item['Title'], 0, 'C');
            $this->SetX($xPos[$currentX]); // Reset X after MultiCell

            $imagePath = $item['Image_location'];
            $imageWidth = 40; // Smaller image for 4-per-page
            if (!empty($imagePath) && file_exists($imagePath) && filesize($imagePath) > 0 && in_array(strtolower(pathinfo($imagePath, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])) {
                // Center image within its cell: $xPos[$currentX] + ($cellWidth - $imageWidth) / 2
                $this->Image($imagePath, $xPos[$currentX] + ($cellWidth - $imageWidth) / 2, $this->GetY(), $imageWidth);
                $this->Ln($imageWidth + 1); // Space after image
            } else {
                $this->SetFont('Arial','',8);
                $this->SetX($xPos[$currentX]);
                $this->MultiCell($cellWidth, 5, '(Image N/A: ' . basename($imagePath) .')', 0, 'C');
                $this->Ln(5);
            }

            $this->SetFont('Arial','',8);
            $this->SetX($xPos[$currentX]);
            $this->MultiCell($cellWidth, 5, 'Pub: ' . $item['Publisher']);
            $this->SetX($xPos[$currentX]);
            $this->MultiCell($cellWidth, 5, 'PEGI: ' . $item['Pegi']);
            // Truncate description heavily
            $desc = strlen($item['Description']) > 50 ? substr($item['Description'],0,47).'...' : $item['Description'];
            $this->SetX($xPos[$currentX]);
            $this->MultiCell($cellWidth,5,'Desc: ' . $desc);
            $this->SetX($xPos[$currentX]);
            $this->MultiCell($cellWidth,5,'Ctrl: ' . $item['Controllers']);

            // Manage position for next item
            if ($currentX == 0) { // If it was the left column
                $currentX = 1; // Move to right column
                $this->SetY($startY); // Reset Y to the start of the row for the second item
            } else { // If it was the right column
                $currentX = 0; // Move back to left column for next row
                // Calculate max Y position of the two items in the row and Ln below that
                // This is simplified; real content height would be needed for perfect alignment
                $this->Ln(5); // Add some space before the next row of items
            }

            $itemCount++;
            if ($itemCount >= 4) break; // Max 4 items per page
        }
    }
}

function generate_pdf(array $data, string $outputFilePath): void
{
    $pdf = new PDF();
    $pdf->AliasNbPages(); // For total page numbers

    // Separate data by type
    $destacados = [];
    $news = [];
    $genresData = []; // Keyed by Genre

    foreach ($data as $item) {
        // Ensure all required keys exist to prevent errors
        $item['Title'] = $item['Title'] ?? 'N/A';
        $item['Publisher'] = $item['Publisher'] ?? 'N/A';
        $item['Pegi'] = $item['Pegi'] ?? 'N/A';
        $item['Description'] = $item['Description'] ?? 'N/A';
        $item['Controllers'] = $item['Controllers'] ?? 'N/A';
        $item['Image_location'] = $item['Image_location'] ?? '';
        $item['Genre'] = $item['Genre'] ?? 'Unknown';
        $item['Type'] = $item['Type'] ?? 'Unknown';


        if (isset($item['Type'])) {
            switch (strtolower($item['Type'])) {
                case 'destacado':
                    $destacados[] = $item;
                    break;
                case 'new':
                    $news[] = $item;
                    break;
                case 'genre':
                    $genre = $item['Genre'] ?? 'Unknown';
                    $genresData[$genre][] = $item;
                    break;
            }
        }
    }

    // Add "Destacado" slides
    foreach ($destacados as $item) {
        $pdf->addDestacadoSlide($item);
    }

    // Add "New" slides (two per slide)
    for ($i = 0; $i < count($news); $i += 2) {
        $item1 = $news[$i];
        $item2 = isset($news[$i+1]) ? $news[$i+1] : null;
        $pdf->addNewSlide($item1, $item2);
    }

    // Add "Genre" slides (four per slide, filtered by genre)
    foreach ($genresData as $genre => $items) {
        for ($i = 0; $i < count($items); $i += 4) {
            $batch = array_slice($items, $i, 4);
            if (!empty($batch)) {
                 $pdf->addGenreSlide($batch, $genre);
            }
        }
    }

    $pdf->Output('F', $outputFilePath);
}

?>
