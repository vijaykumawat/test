<?php
namespace App\Libraries;

class CsvImporter
{
    private $file;
    private $mapping;
    private $dbFields = [
        'regDate','regDateMonth','regNumber','ownerName',
        'address','vehicleMaker','vehicleModel','fuelType','saleAmt',
        'seatCapacity','mobile','expiryDate','prevInsuCompany','finance','telecaller'
    ];

    public function __construct($file, $mappingJson = null)
    {
        $this->file    = $file;
        $this->mapping = $mappingJson ? json_decode($mappingJson, true) : null;
    }

    public function process(): array
    {
        if (!$this->file || !$this->file->isValid()) {
            throw new \Exception('Invalid CSV file');
        }

        $stream = fopen($this->file->getTempName(), 'r');
        if ($stream === false) throw new \Exception('Unable to read uploaded file');

        $headers = fgetcsv($stream);
        if ($headers === false) throw new \Exception('CSV appears empty or invalid');

        // Normalize headers
        $headers = array_map(fn($h) => preg_replace('/^\x{FEFF}/u', '', trim((string)$h)), $headers);

        // Auto-mapping if none provided
        if (!$this->mapping) {
            $lowerHeaders = array_map('mb_strtolower', $headers);
            $mapping = [];
            foreach ($this->dbFields as $f) {
                $pos = array_search(mb_strtolower($f), $lowerHeaders);
                if ($pos === false) throw new \Exception("Mapping required: missing {$f}");
                $mapping[$f] = $headers[$pos];
            }
            $this->mapping = $mapping;
        }

        // Build header index lookup
        $headerIndex = [];
        foreach ($headers as $i => $h) {
            $headerIndex[mb_strtolower($h)] = $i;
        }

        $rows = [];
        while (($csvRow = fgetcsv($stream)) !== false) {
            if (!array_filter($csvRow, fn($c) => trim((string)$c) !== '')) continue;

            $rowData = ['recordId' => $this->generateRecordId()];
            foreach ($this->dbFields as $field) {
                $csvHeader = $this->mapping[$field];
                $idx = $headerIndex[mb_strtolower($csvHeader)] ?? null;
                $rowData[$field] = ($idx !== null && isset($csvRow[$idx])) ? trim($csvRow[$idx]) : null;
            }

            // Auto-fill system fields
            $rowData['dataUploadDate'] = date('Y-m-d H:i:s');
            $rowData['actionTaken']    = 0;
            $rowData['isImportant']    = 0;
            $rowData['alreadySale']    = 0;
            $rowData['modifiyDate']    = date('Y-m-d H:i:s');
            $rowData['isIntrested']    = 0;
            $rowData['saleInGb']       = 0;

            $rows[] = $rowData;
        }
        fclose($stream);

        if (empty($rows)) throw new \Exception('No data rows found in CSV');
        return $rows;
    }

    private function generateRecordId(): string
    {
        return substr(md5(uniqid()), 0, 16);
    }
}
