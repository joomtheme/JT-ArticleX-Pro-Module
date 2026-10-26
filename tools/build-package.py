#!/usr/bin/env python3
"""Build a deterministic Joomla install ZIP from the versioned module sources."""
from pathlib import Path
import xml.etree.ElementTree as ET
import zipfile

root = Path(__file__).resolve().parents[1]
source = root / 'mod_jtarticlexpro'
version = ET.parse(source / 'mod_jtarticlexpro.xml').getroot().findtext('version')
target = root / f'mod_jtarticlexpro_v{version}.zip'
with zipfile.ZipFile(target, 'w', compression=zipfile.ZIP_DEFLATED, compresslevel=9) as archive:
    for path in sorted(source.rglob('*')):
        if path.is_file():
            entry = zipfile.ZipInfo(path.relative_to(source).as_posix(), (2026, 1, 1, 0, 0, 0))
            entry.create_system = 3
            entry.external_attr = 0o100644 << 16
            entry.compress_type = zipfile.ZIP_DEFLATED
            archive.writestr(entry, path.read_bytes(), compresslevel=9)
print(target)
