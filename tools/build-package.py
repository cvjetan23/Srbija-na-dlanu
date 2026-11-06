#!/usr/bin/env python3
"""Build an installable WordPress ZIP with deterministic paths and metadata."""
from pathlib import Path
import hashlib
import re
import zipfile

ROOT = Path(__file__).resolve().parents[1]
PLUGIN = ROOT / "srbija-na-dlanu"
OUTPUT = ROOT / "dist"


def main():
    if not (PLUGIN / "srbija-na-dlanu.php").is_file():
        raise SystemExit("Missing plugin entry point.")
    entry = (PLUGIN / "srbija-na-dlanu.php").read_text(encoding="utf-8")
    readme = (PLUGIN / "readme.txt").read_text(encoding="utf-8")
    header = re.search(r"^\s*\* Version:\s*(\S+)", entry, re.M)
    constant = re.search(r"define\('SND_VERSION',\s*'([^']+)'\)", entry)
    stable = re.search(r"^Stable tag:\s*(\S+)", readme, re.M)
    if not header or not constant or not stable or len({header[1], constant[1], stable[1]}) != 1:
        raise SystemExit("Plugin version/header/stable tag mismatch.")
    OUTPUT.mkdir(exist_ok=True)
    archive = OUTPUT / "srbija-na-dlanu.zip"
    files = sorted(path for path in PLUGIN.rglob("*") if path.is_file())
    with zipfile.ZipFile(archive, "w", compression=zipfile.ZIP_DEFLATED, compresslevel=9) as package:
        for path in files:
            if path.is_symlink():
                raise SystemExit(f"Symlink cannot be packaged: {path}")
            relative = path.relative_to(ROOT).as_posix()
            info = zipfile.ZipInfo(relative, (2026, 1, 1, 0, 0, 0))
            info.create_system = 3
            info.external_attr = 0o100644 << 16
            info.compress_type = zipfile.ZIP_DEFLATED
            package.writestr(info, path.read_bytes(), compress_type=zipfile.ZIP_DEFLATED, compresslevel=9)
    with zipfile.ZipFile(archive) as package:
        if package.testzip():
            raise SystemExit("ZIP integrity check failed.")
        expected = {path.relative_to(ROOT).as_posix(): path for path in files}
        if set(package.namelist()) != set(expected):
            raise SystemExit("ZIP/source path mismatch.")
        for name, source in expected.items():
            if package.read(name) != source.read_bytes():
                raise SystemExit(f"ZIP/source content mismatch: {name}")
    digest = hashlib.sha256(archive.read_bytes()).hexdigest()
    (OUTPUT / "SHA256SUMS").write_text(f"{digest}  {archive.name}\n", encoding="utf-8")
    print(f"{archive.relative_to(ROOT)}: {archive.stat().st_size:,} bytes; {len(files)} files")
    print(f"SHA256 {digest}")


if __name__ == "__main__":
    main()
