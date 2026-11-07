#!/usr/bin/env python3
"""Build the decorative SVG from public-domain Natural Earth 1:10m river lines.

Usage: PYTHONPATH=/path/to/shapely python tools/build-rivers.py input.geojson
Source: nvkelso/natural-earth-vector/geojson/ne_10m_rivers_lake_centerlines.geojson
The plugin needs only the generated SVG, not Python or GIS dependencies.
"""
import json
import math
from pathlib import Path
import sys
from shapely.geometry import shape, box

names = {'Danube': 'Dunav', 'Sava': 'Sava', 'Drina': 'Drina', 'Morava': 'Morava', 'Tisa': 'Tisa'}
clip = box(19.05, 42.15, 22.9, 45.65)
def point(x, y):
    return ((x - 19.05) * math.cos(math.radians(44)) * 265, (45.65-y) * 265)

parts = ['<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 735 928" fill="none">',
         '<!-- Natural Earth public domain, 1:10m rivers; cropped decorative context, not a navigation map. -->',
         '<g stroke="#85a18c" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">']
labels = []
for feature in json.loads(Path(sys.argv[1]).read_text())['features']:
    name = feature['properties'].get('name')
    if name not in names:
        continue
    g = shape(feature['geometry']).intersection(clip).simplify(.0015)
    if g.is_empty or g.length < .1:
        continue
    lines = [g] if g.geom_type == 'LineString' else list(g.geoms)
    for line in lines:
        if line.geom_type != 'LineString':
            continue
        coords = [point(x, y) for x, y in line.coords]
        d = 'M' + 'L'.join(f'{x:.2f},{y:.2f}' for x, y in coords)
        parts.append(f'<path d="{d}"/>')
    longest = max(lines, key=lambda line: line.length)
    p = longest.interpolate(.52, normalized=True)
    x, y = point(p.x, p.y)
    labels.append(f'<text x="{x+6:.1f}" y="{y-8:.1f}">{names[name]}</text>')
parts.extend(['</g>', '<g fill="#85a18c" font-family="Arial,sans-serif" font-size="10" letter-spacing="1">', *labels, '</g></svg>'])
target = Path(__file__).resolve().parents[1] / 'srbija-na-dlanu/assets/rivers.svg'
target.write_text('\n'.join(parts)+'\n')
print(f'{target.name}: {target.stat().st_size} bytes, {len(labels)} labels')
