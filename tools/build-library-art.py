#!/usr/bin/env python3
"""Create an original decorative atlas from this project's sourced geography."""
import json,re,math
from pathlib import Path
from xml.sax.saxutils import escape
ROOT=Path(__file__).resolve().parents[1]
PLUGIN=ROOT/'srbija-na-dlanu'
def simplify(points,tolerance=.9):
 if len(points)<3:return points
 a,b=points[0],points[-1];dx,dy=b[0]-a[0],b[1]-a[1];length=dx*dx+dy*dy
 def distance(p):
  t=max(0,min(1,((p[0]-a[0])*dx+(p[1]-a[1])*dy)/length)) if length else 0
  return math.hypot(p[0]-a[0]-t*dx,p[1]-a[1]-t*dy)
 i=max(range(1,len(points)-1),key=lambda i:distance(points[i]))
 if distance(points[i])<=tolerance:return [a,b]
 return simplify(points[:i+1],tolerance)[:-1]+simplify(points[i:],tolerance)
def path(d):
 out=[]
 for part in d.split('M')[1:]:
  nums=list(map(float,re.findall(r'-?\d+(?:\.\d+)?',part)));points=simplify(list(zip(nums[::2],nums[1::2])))
  if points:out.append('M'+'L'.join(f'{x:.1f},{y:.1f}' for x,y in points)+('Z' if 'Z'in part else ''))
 return ''.join(out)
text=(PLUGIN/'data/territories.php').read_text();districts=[t for t in json.loads(re.search(r"<<<'[^']+'\n(.*?)\n[A-Z_]+",text,re.S)[1])['territories'] if t['kind']=='district']
items=json.loads((PLUGIN/'data/explore.json').read_text())['items']
rivers=['Dunav','Sava','Tisa','Drina','Velika Morava','Zapadna Morava','Južna Morava']
mountains=['Fruška gora','Zlatibor','Kopaonik','Stara planina']
s=['<svg xmlns="http://www.w3.org/2000/svg" viewBox="-30 -15 660 820" fill="none" color="#2d6a4f">','<title>Srbija: okruzi, reke i planine</title>','<metadata>Original decorative illustration. District boundaries: geoBoundaries CC BY 4.0 and MRE Kosovo and Metohija. Rivers: OpenStreetMap ODbL. Peak reference points: PSS. See SOURCES.md and EXPLORE-SOURCES.md.</metadata>','<g stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" opacity=".65">']
for t in districts:s.append(f'<path d="{path(t["map_path"])}"/>')
s.append('</g><g stroke="currentColor" stroke-width="3.6" stroke-linecap="round" stroke-linejoin="round">')
for name in rivers:
 t=next(t for t in items if t['kind']=='river' and t['name']==name);s.append(f'<path d="{path(t["map_path"])}"/>')
s.append('</g><g stroke="currentColor" stroke-width="3.5" stroke-linejoin="round">')
for name in mountains:
 t=next(t for t in items if t['kind']=='mountain' and t['name']==name);x,y=t['point'];s.append(f'<g transform="translate({x:.1f} {y:.1f})"><path d="M-24 14 0-21 24 14Z"/><path d="m-9-8 9 8 8-8"/></g>')
s.append('</g><g fill="currentColor"><circle cx="240" cy="285" r="5"/><circle cx="380" cy="505" r="4"/></g></svg>')
p=PLUGIN/'assets/library-atlas.svg';p.write_text('\n'.join(s)+'\n');print(f'{p.relative_to(ROOT)}: {p.stat().st_size:,} bytes; 30 districts, seven rivers and four mountain symbols.')
