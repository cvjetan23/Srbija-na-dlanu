#!/usr/bin/env python3
"""Restore immutable, attributed offline photographs; verify bundle and image SHA256.

Wikimedia thumbnail servers can recompress the same URL. Reviewed photograph
bytes are therefore versioned in small ZIP source bundles. Builds and installed
WordPress never contact a third-party image/data service.
"""
from pathlib import Path
import hashlib,json,re,zipfile
ROOT=Path(__file__).resolve().parents[1]
def main():
    entries=json.loads((ROOT/'tools/explore-assets.json').read_text())
    paths={e['path']:e for e in entries}
    names={Path(e['path']).name:e for e in entries}
    if len(paths)!=len(entries) or len(names)!=len(entries):raise ValueError('Repeated photo path')
    for relative in paths:
        if not re.fullmatch(r'srbija-na-dlanu/assets/photos/v4/[a-f0-9]{24}\.(jpg|png|webp)',relative):raise ValueError('Invalid photo path')
    restored=set()
    for bundle in json.loads((ROOT/'tools/explore-photo-bundles.json').read_text()):
        relative=bundle['path']
        if not re.fullmatch(r'tools/explore-photo-bundles/[0-9]{2}\.zip',relative):raise ValueError('Invalid bundle path')
        path=ROOT/relative;content=path.read_bytes()
        if len(content)!=bundle['bytes'] or hashlib.sha256(content).hexdigest()!=bundle['sha256']:raise ValueError('Bundle checksum mismatch: '+relative)
        with zipfile.ZipFile(path) as package:
            if len(package.infolist())!=bundle['files']:raise ValueError('Bundle member count mismatch')
            for member in package.infolist():
                e=names.get(member.filename)
                if not e or member.filename in restored or member.file_size!=e['bytes'] or member.file_size>500000:raise ValueError('Invalid bundled member')
                image=package.read(member)
                if hashlib.sha256(image).hexdigest()!=e['sha256']:raise ValueError('Photo checksum mismatch')
                target=ROOT/e['path'];target.parent.mkdir(parents=True,exist_ok=True)
                if not target.exists() or target.read_bytes()!=image:target.write_bytes(image)
                restored.add(member.filename)
    if restored!=set(names):raise ValueError('Incomplete photo bundles')
    folder=ROOT/'srbija-na-dlanu/assets/photos/v4'
    for path in folder.glob('*'):
        if path.is_file() and path.relative_to(ROOT).as_posix() not in paths:path.unlink()
    print(f'Verified {len(entries)} attributed offline photo files ({sum(e["bytes"] for e in entries):,} bytes).')
if __name__=='__main__':main()
