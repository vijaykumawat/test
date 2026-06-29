<?php

class OcrHelper {
    public static function runPython(string $imagePath): array {
        $scriptPath = ROOTPATH.'ocr.py';
        if (!file_exists($scriptPath)) return ['error'=>'OCR script not found'];
        foreach (['python','python3','py -3'] as $cmd) {
            $output = shell_exec("$cmd ".escapeshellarg($scriptPath)." ".escapeshellarg($imagePath)." 2>&1");
            $result = json_decode($output,true);
            if (is_array($result)) return $result;
        }
        return ['error'=>'OCR failed'];
    }
}