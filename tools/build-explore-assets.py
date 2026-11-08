#!/usr/bin/env python3
"""Hydrate pinned, attributed Commons photographs into the self-contained WP ZIP.

Only public HTTPS image URLs on Wikimedia's two image hosts are accepted. Every
byte is SHA256-pinned; source pages and licences are in data/explore.json. The
installed plugin never downloads these files or calls a third-party data API.
"""
from pathlib import Path
import concurrent.futures,hashlib,json,re,time,urllib.request,urllib.parse,urllib.error
ROOT=Path(__file__).resolve().parents[1]
MANIFEST=ROOT/'tools/explore-assets.json'

def normalize_image(content):
    # Thumbnail servers attach variable EXIF/JFIF comments. Keep compressed
    # image data unchanged; signatures pin every encoded pixel, not metadata.
    if content[:2] != b"\xff\xd8":return content
    result=bytearray(content[:2]);offset=2
    while offset<len(content):
        if content[offset]!=255:raise ValueError("Invalid JPEG marker")
        start=offset
        while offset<len(content) and content[offset]==255:offset+=1
        marker=content[offset];offset+=1
        if marker in (0xda,0xd9):result.extend(content[start:]);return bytes(result)
        size=int.from_bytes(content[offset:offset+2],"big")
        if size<2 or offset+size>len(content):raise ValueError("Invalid JPEG segment")
        if not (0xe0<=marker<=0xed or marker in (0xef,0xfe)):result.extend(content[start:offset+size])
        offset+=size
    raise ValueError("Incomplete JPEG")

def hydrate(entry):
    relative=entry['path']
    if not re.fullmatch(r'srbija-na-dlanu/assets/photos/v4/[a-f0-9]{24}\.(jpg|png|webp)',relative):raise ValueError('Invalid asset path')
    url=urllib.parse.urlparse(entry['url'])
    if url.scheme!='https' or url.hostname not in ('upload.wikimedia.org','thumb.wikimedia.org'):raise ValueError('Invalid image origin')
    target=ROOT/relative
    if target.exists():
        content=normalize_image(target.read_bytes())
        if hashlib.sha256(content).hexdigest()==entry['sha256']:
            if content!=target.read_bytes():target.write_bytes(content)
            return
    for attempt in range(3):
        try:
            req=urllib.request.Request(entry['url'],headers={'User-Agent':'SrbijaNaDlanu/4.0 (https://cvjetan.dev; reproducible offline educational assets)'})
            with urllib.request.urlopen(req,timeout=90) as response:content=normalize_image(response.read(500001))
            if len(content)!=entry['bytes'] or hashlib.sha256(content).hexdigest()!=entry['sha256']:raise ValueError('Image checksum mismatch: '+relative+'; received '+str(len(content))+' bytes / '+hashlib.sha256(content).hexdigest()+'; type '+str(response.headers.get('Content-Type')))
            target.parent.mkdir(parents=True,exist_ok=True);target.write_bytes(content);return
        except urllib.error.HTTPError as error:
            if error.code==429 and attempt<2:time.sleep(min(30,int(error.headers.get('Retry-After','5'))));continue
            raise
        except (urllib.error.URLError,TimeoutError):
            if attempt==2:raise
            time.sleep(2)

def main():
    entries=json.loads(MANIFEST.read_text());paths={e['path'] for e in entries}
    if len(paths)!=len(entries):raise ValueError('Repeated photo path')
    with concurrent.futures.ThreadPoolExecutor(max_workers=4) as pool:list(pool.map(hydrate,entries))
    # Only generated, manifested assets belong in the release.
    folder=ROOT/'srbija-na-dlanu/assets/photos/v4'
    for path in folder.glob('*'):
        if path.is_file() and path.relative_to(ROOT).as_posix() not in paths:path.unlink()
    print(f'Verified {len(entries)} attributed photo files ({sum(e["bytes"] for e in entries):,} bytes).')
if __name__=='__main__':main()
