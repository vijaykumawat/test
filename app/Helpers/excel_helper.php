<?php

if (!function_exists('exportToExcel')) {
    /**
     * Export array of data to Excel file
     */
    function exportToExcel($data, $filename, $headers = [])
    {
        // Build HTML table
        $html = "<table border='1'>";

        // Add headers
        if (!empty($headers)) {
            $html .= "<tr>";
            foreach ($headers as $header) {
                $html .= "<th>" . htmlspecialchars($header) . "</th>";
            }
            $html .= "</tr>";
        }

        // Add data rows
        foreach ($data as $row) {
            $html .= "<tr>";
            foreach ($row as $cell) {
                $value = is_array($cell) ? implode(", ", $cell) : $cell;
                $html .= "<td>" . htmlspecialchars($value) . "</td>";
            }
            $html .= "</tr>";
        }

        $html .= "</table>";

        // Force headers for download (raw PHP headers are safer here)
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Add UTF-8 BOM for Excel
        echo "\xEF\xBB\xBF";
        echo $html;

        exit;
    }
}


if (!function_exists('arrayToExcelHtml')) {
    /**
     * Convert array to HTML table for Excel
     */
    function arrayToExcelHtml($data, $headers = [])
    {
        $html = "<table border='1'>";

        if (!empty($headers)) {
            $html .= "<tr>";
            foreach ($headers as $header) {
                $html .= "<th>" . htmlspecialchars($header) . "</th>";
            }
            $html .= "</tr>";
        }

        foreach ($data as $row) {
            $html .= "<tr>";
            foreach ($row as $cell) {
                $value = is_array($cell) ? implode(", ", $cell) : $cell;
                $html .= "<td>" . htmlspecialchars($value) . "</td>";
            }
            $html .= "</tr>";
        }

        $html .= "</table>";

        return $html;
    }
}


if (!function_exists('policyTableToExcel')) {
    /**
     * Convert policy table data to Excel format
     */
    function policyTableToExcel($policies, $filename = 'policies.xls')
    {
        $headers = [
            '#',
            'Policy No.',
            'Holder Name',
            'Company',
            'Vehicle No.',
            'Insurance Type',
            'Issue Date',
            'Expiry Date'
        ];

        $data = [];
        foreach ($policies as $index => $policy) {
            $data[] = [
                $index + 1,
                $policy['policy_number'] ?? 'N/A',
                $policy['holder_name'] ?? 'N/A',
                $policy['company_name'] ?? 'N/A',
                $policy['vehicle_number'] ?? 'N/A',
                $policy['insurance_type'] ?? 'N/A',
                $policy['issue_date'] ?? 'N/A',
                $policy['expiry_date'] ?? 'N/A'
            ];
        }

        exportToExcel($data, $filename, $headers);
    }
}
