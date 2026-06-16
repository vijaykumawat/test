from pathlib import Path
text = Path('app/Controllers/Admin.php').read_text()
line = 1
stack = []
for idx, ch in enumerate(text, 1):
    if ch == '\n':
        line += 1
    elif ch == '{':
        stack.append((idx, line))
    elif ch == '}':
        if stack:
            stack.pop()
        else:
            print('extra_close', idx, line)
if stack:
    print('unclosed_open_count', len(stack))
    for pos, l in stack[-10:]:
        print('unclosed_open_at', pos, l)
else:
    print('balanced')
print('total opens', text.count('{'), 'total closes', text.count('}'))
