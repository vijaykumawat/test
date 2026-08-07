<?php

use App\Libraries\OCRProcessor;

/**
 * Validate uploaded payment screenshot using OCR.
 *
 * @param string $imagePath Path to uploaded image
 * @param OCRProcessor $ocrProcessor OCR processor instance
 * @return array Result array with either 'error' or 'success' + parsed data
 */
function validateImage(string $imagePath, OCRProcessor $ocrProcessor): array
{
    $ocrResult = $ocrProcessor->runOcr($imagePath);

    if (! empty($ocrResult['error'])) {
        return ['error' => 'OCR failed: ' . $ocrResult['error']];
    }

    $validNames = [
        "Vijay Kailas Kumawat",
        "Vijey Kumawatt",
        "Vijay Kailash Kumawat",
        "Vijay Kumawat"
    ];

    $text          = trim($ocrResult['text'] ?? '');
    $receiverValid = containsReceiverName($text, $validNames);
    $dateText      = extractDateFromText($text);
    $dateValid     = isTodayDate($dateText);
    $transactionId = extractTransactionId($text);
    $amt           = extractAmount($text);
    $utr           = extractUTR($text);

    //return ['error' => '', 'text::' => $text, 'dateText::' => $dateText, 'transactionId::' => $transactionId, 'amount::' => $amt, 'utr::' => $utr, 'receiverValid::' => $receiverValid, 'dateValid::' => $dateValid];
    
    //return ['error' =>  $dateText . '::amt::'  . $amt . '::UTR::' . $utr . '::' . ($receiverValid ? 'true' : 'false') . '::' . ($dateValid ? 'true' : 'false')];

    if (! $receiverValid) {
        return ['error' => 'This payment is done for an invalid receiver.'];
    }
    
    if (! $dateValid) {
        return ['error' => 'Invalid payment date.'];
    }
        
    if ($amt < 249) {
        return ['error' => 'Invalid payment amount (' . $amt . ').'];
    }

    // ✅ If all checks pass, return parsed data
    return [
        'success'       => 'Image validated successfully.',
        'text'          => $text,
        'dateText'      => $dateText,
        'transactionId' => $transactionId,
        'amount'        => $amt,
        'utr'           => $utr
    ];
}

    function containsReceiverName(string $text, array $expectedNames): bool
    {
        // Normalize the input text
        $normalizedText = strtolower(preg_replace('/\s+/', ' ', $text));

        foreach ($expectedNames as $expected) {
            // Normalize expected name
            $expectedClean = strtolower(trim(preg_replace('/\s+/', ' ', $expected)));

            // Simple substring check
            if (strpos($normalizedText, $expectedClean) !== false) {
                return true;
            }
        }

        return false;
    }
     function extractDateFromText(string $text): ?string
    {
        $patterns = [
            '/\b(\d{1,2}[\/-]\d{1,2}[\/-]\d{2,4})\b/',
            '/\b(\d{4}[\/-]\d{1,2}[\/-]\d{1,2})\b/',
            '/\b([A-Za-z]{3,9}\s+\d{1,2},?\s+\d{4})\b/',
            '/\b(\d{1,2}\s+[A-Za-z]{3,9}\s+\d{4})\b/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches)) {
                foreach ($matches[1] as $candidate) {
                    $normalized = normalizeDateString($candidate);
                    if ($normalized !== null) {
                        return $normalized;
                    }
                }
            }
        }

        return null;
    }

    function normalizeDateString(string $dateStr): ?string
    {
        $formats = ['d/m/Y', 'd-m-Y', 'd/m/y', 'd-m-y', 'Y-m-d', 'Y/m/d', 'M d, Y', 'F d, Y', 'd M Y', 'd F Y'];
        foreach ($formats as $format) {
            $dt = \DateTime::createFromFormat($format, trim($dateStr));
            if ($dt !== false) {
                return $dt->format('Y-m-d');
            }
        }

        return null;
    }

    function isTodayDate(?string $dateStr): bool
    {
        if (empty($dateStr)) {
            return false;
        }

        return $dateStr === date('Y-m-d');
    }

    function extractTransactionId($input) {
        // Regex looks for "Transaction ID" followed by a space and an alphanumeric string
        $pattern = '/Transaction ID\s+([A-Za-z0-9]+)/i';
        if (preg_match($pattern, $input, $matches)) {
            return $matches[1]; // Transaction ID
        }
        return null; // Not found
    }
    function extractAmount($input) {
        // Regex looks for ? or ₹ or Rs followed by digits
        $pattern = '/[₹?Rs]\s?(\d+(?:\.\d{1,2})?)/i';
        if (preg_match($pattern, $input, $matches)) {
            return $matches[1]; // Amount as string
        }
        return null; // Not found
    }
    function extractUTR($input) {
        // Regex looks for "UTR:" followed by digits
        $pattern = '/UTR:\s*([0-9]+)/i';
        if (preg_match($pattern, $input, $matches)) {
            return $matches[1]; // UTR number
        }
        return null; // Not found
    }

