<?php
$code = file_get_contents('app/Controllers/Admin.php');
$tokens = token_get_all($code);
$stack = [];
$line = 1;
foreach ($tokens as $token) {
    if (is_array($token)) {
        $text = $token[1];
        $line += substr_count($text, "\n");
        if ($token[0] === T_COMMENT || $token[0] === T_DOC_COMMENT || $token[0] === T_CONSTANT_ENCAPSED_STRING || $token[0] === T_ENCAPSED_AND_WHITESPACE) {
            continue;
        }
        $text = $token[1];
    } else {
        $text = $token;
    }
    for ($i = 0; $i < strlen($text); $i++) {
        $ch = $text[$i];
        if ($ch === '{') {
            $stack[] = ['line' => $line, 'char' => $ch];
        } elseif ($ch === '}') {
            if ($stack) {
                array_pop($stack);
            } else {
                echo "extra_close at line $line\n";
            }
        }
    }
}
if ($stack) {
    echo "unclosed_open_count " . count($stack) . "\n";
    foreach ($stack as $item) {
        echo "unclosed_open_at line " . $item['line'] . "\n";
    }
} else {
    echo "balanced\n";
}
echo "total opens " . count(array_filter($stack, fn($x)=>true)) . "\n";
