<?php

function validateUpload($file, array $allowedExtensions, string $uploadPath) {
    if (!$file || !$file->isValid()) {
        return ['success' => false, 'message' => 'Invalid file'];
    }
    $ext = strtolower($file->getClientExtension());
    if (!in_array($ext, $allowedExtensions)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);
    $newName = $file->getRandomName();
    if (!$file->move($uploadPath, $newName)) {
        return ['success' => false, 'message' => 'Failed to save file'];
    }
    return ['success' => true, 'path' => $uploadPath.$newName, 'name' => $newName];
}
