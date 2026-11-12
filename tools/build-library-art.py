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
s=['<svg xmlns="http://www.w3.org/2000/svg" viewBox="-50 -15 700 820" fill="none">','<title>Srbija: okruzi, reke i planine</title>','<metadata>Original illustration from the bundled atlas. District boundaries: geoBoundaries CC BY 4.0 and MRE Kosovo and Metohija boundaries. Rivers: OpenStreetMap ODbL. Peak reference points: PSS. See SOURCES.md and EXPLORE-SOURCES.md. Illustrative mountain symbols are original.</metadata>','<path d="M40 235C-35 385 21 646 219 716C386 802 650 651 621 443C607 275 421 75 231 129C132 145 82 174 40 235Z" fill="#95d5b2" opacity=".35"/>','<g stroke="#ffffff" stroke-width="2" opacity=".55"><ellipse cx="300" cy="420" rx="300" ry="303"/><ellipse cx="300" cy="420" rx="270" ry="275"/><ellipse cx="300" cy="420" rx="240" ry="247"/></g>']
s.append('<g stroke="#2d6a4f" stroke-width="1.5" stroke-linejoin="round">')
for i,t in enumerate(districts):s.append(f'<path d="{path(t["map_path"])}" fill="'+('#95d5b2' if i in [3,8,13,20,27] else '#f4f7ed')+'"/>')
s.append('</g>')
for stroke,width in [('#ffffff',8),('#2d6a4f',3.6)]:
 s.append(f'<g stroke="{stroke}" stroke-width="{width}" stroke-linecap="round" stroke-linejoin="round">')
 for name in rivers:
  t=next(t for t in items if t['kind']=='river' and t['name']==name);s.append(f'<path d="{path(t["map_path"])}"/>')
 s.append('</g>')
s.append('<g fill="#2d6a4f" stroke="#ffffff" stroke-width="2.5" stroke-linejoin="round">')
for name in mountains:
 t=next(t for t in items if t['kind']=='mountain' and t['name']==name);x,y=t['point'];s.append(f'<g transform="translate({x:.1f} {y:.1f})"><circle r="28" fill="#f4f7ed" stroke="none"/><path d="M-24 14 0-21 24 14Z"/><path d="m-9-8 9 8 8-8" fill="none"/></g>')
s.append('</g><g fill="#2d6a4f"><circle cx="240" cy="285" r="6"/><circle cx="380" cy="505" r="5"/></g></svg>')
p=PLUGIN/'assets/library-atlas.svg';p.write_text('\n'.join(s)+'\n');print(f'{p.relative_to(ROOT)}: {p.stat().st_size:,} bytes; 30 districts, seven rivers and four mountain symbols.')
