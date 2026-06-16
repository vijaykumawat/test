<?php

namespace App\Libraries;

class OCRProcessor
{
    /**
     * Extract text from image using OCR
     */
    public function extractTextFromImage($imagePath)
    {
        try {
            // Check if file exists
            if (!file_exists($imagePath)) {
                return [
                    'error' => 'File not found',
                    'text' => ''
                ];
            }

            // Use system command to extract text (requires tesseract or similar)
            $output = shell_exec("tesseract \"" . escapeshellarg($imagePath) . "\" stdout 2>/dev/null");
            
            if ($output === null) {
                return [
                    'error' => 'OCR engine not available',
                    'text' => ''
                ];
            }

            $text = trim($output);
            $transaction = $this->parseTransactionData($text);

            return [
                'text' => $text,
                'transaction' => $transaction
            ];
        } catch (\Exception $e) {
            log_message('error', 'OCR extraction error: ' . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'text' => ''
            ];
        }
    }

    /**
     * Parse transaction data from extracted text
     */
    private function parseTransactionData($text)
    {
        $transaction = [
            'receiver_name' => '',
            'amount' => '',
            'transaction_date' => '',
            'transaction_id' => ''
        ];

        // Extract receiver name
        if (preg_match('/(?:to|receiver|payee|beneficiary)\s*[:\-]?\s*([A-Za-z\s]+?)(?:\n|amount|INR|Rs)/i', $text, $matches)) {
            $transaction['receiver_name'] = trim($matches[1]);
        }

        // Extract amount
        if (preg_match('/(?:amount|rs|₹)\s*[:\-]?\s*([\d,]+(?:\.\d{2})?)/i', $text, $matches)) {
            $transaction['amount'] = trim($matches[1]);
        }

        // Extract transaction date
        if (preg_match('/(?:date|on)\s*[:\-]?\s*(\d{2}[\-\/]\d{2}[\-\/]\d{4}|\d{4}[\-\/]\d{2}[\-\/]\d{2})/i', $text, $matches)) {
            $transaction['transaction_date'] = $this->parseDate($matches[1]);
        }

        // Extract transaction ID or reference
        if (preg_match('/(?:id|reference|ref\.?|txn|transaction)\s*[:\-]?\s*([A-Z0-9\-]+)/i', $text, $matches)) {
            $transaction['transaction_id'] = trim($matches[1]);
        }

        return $transaction;
    }

    /**
     * Parse date in various formats
     */
    private function parseDate($dateString)
    {
        $dateString = trim($dateString);
        
        try {
            $date = \DateTime::createFromFormat('d/m/Y', $dateString) 
                    ?? \DateTime::createFromFormat('d-m-Y', $dateString)
                    ?? \DateTime::createFromFormat('m/d/Y', $dateString)
                    ?? \DateTime::createFromFormat('Y-m-d', $dateString);
            
            return $date ? $date->format('Y-m-d') : date('Y-m-d');
        } catch (\Exception $e) {
            return date('Y-m-d');
        }
    }
}
