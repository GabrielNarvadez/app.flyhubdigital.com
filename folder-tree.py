import os

# File types to include
ALLOWED_EXTENSIONS = {'.php', '.html', '.css'}

def tree(dir_path='.', prefix=''):
    try:
        entries = sorted(os.listdir(dir_path))
    except (PermissionError, FileNotFoundError):
        return

    # Filter entries to only include directories and allowed files
    visible_entries = [
        e for e in entries
        if os.path.isdir(os.path.join(dir_path, e)) or os.path.splitext(e)[1] in ALLOWED_EXTENSIONS
    ]

    entries_count = len(visible_entries)

    for index, entry in enumerate(visible_entries):
        full_path = os.path.join(dir_path, entry)
        connector = '├── ' if index < entries_count - 1 else '└── '

        print(f"{prefix}{connector}{entry}")

        if os.path.isdir(full_path):
            extension = '│   ' if index < entries_count - 1 else '    '
            tree(full_path, prefix + extension)

if __name__ == '__main__':
    import sys
    path = sys.argv[1] if len(sys.argv) > 1 else '.'
    print(f"{os.path.abspath(path)}")
    tree(path)
