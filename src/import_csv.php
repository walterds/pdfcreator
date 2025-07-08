<?php

function import_csv(string $csvFilePath): array
{
    $data = [];
    if (!file_exists($csvFilePath) || !is_readable($csvFilePath)) {
        trigger_error("CSV file does not exist or is not readable: " . $csvFilePath, E_USER_ERROR);
        return $data;
    }

    if (($handle = fopen($csvFilePath, 'r')) !== false) {
        // Get the header row
        $header = fgetcsv($handle);
        if ($header === false) {
            trigger_error("Could not read header row from CSV file: " . $csvFilePath, E_USER_ERROR);
            fclose($handle);
            return $data;
        }

        // Read data rows
        while (($row = fgetcsv($handle)) !== false) {
            if (count($header) == count($row)) {
                $data[] = array_combine($header, $row);
            } else {
                // Log or handle rows with incorrect column count
                trigger_error("Row with incorrect column count found in CSV file: " . $csvFilePath . " Row: " . implode(',', $row), E_USER_WARNING);
            }
        }
        fclose($handle);
    } else {
        trigger_error("Could not open CSV file: " . $csvFilePath, E_USER_ERROR);
    }
    return $data;
}

?>
