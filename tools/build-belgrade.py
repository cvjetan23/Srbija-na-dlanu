#!/usr/bin/env python3
"""Build 17 Belgrade boroughs from the MRE layer and the SORS 2022 workbook.

Inputs in --sources-root: ministry-4.geojson, ministry-5.geojson, ADM1.geojson,
census.xlsx. Geometry is simplified in UTM 34N, matching the original dataset.
"""
import argparse, json, math, pathlib, re, unicodedata
import openpyxl
from shapely.geometry import shape
from shapely import transform as transform_geometry
def transform(fn,g):return transform_geometry(g,fn,interleaved=False)
from pyproj import Transformer

parser=argparse.ArgumentParser();parser.add_argument('--sources-root',type=pathlib.Path,required=True);args=parser.parse_args()
root=pathlib.Path(__file__).resolve().parents[1];inputs=args.sources_root
def norm(s):
    s=str(s).lower().replace('đ','dj');return re.sub('[^a-z0-9]+',' ',''.join(c for c in unicodedata.normalize('NFD',s) if not unicodedata.combining(c))).strip()
seed=json.loads(re.search(r"<<<'[^']+'\n(.*?)\n[A-Z_]+",(root/'srbija-na-dlanu/data/territories.php').read_text(),re.S)[1]);byid={t['id']:t for t in seed['territories']}
forward=Transformer.from_crs(4326,32634,always_xy=True).transform;back=Transformer.from_crs(32634,4326,always_xy=True).transform
def prep(g):return transform(back,transform(forward,g).simplify(85,preserve_topology=True))
districts=[prep(shape(f['geometry'])) for f in json.loads((inputs/'ADM1.geojson').read_text())['features']]
kim={norm(byid[f'rs-{i:02}']['name']) for i in range(25,30)}
districts.extend(prep(shape(f['geometry'])) for f in json.loads((inputs/'ministry-5.geojson').read_text())['features'] if norm(f['properties']['Value_sl']) in kim)
assert len(districts)==30
coslat=math.cos(math.radians(44));bounds=[g.bounds for g in districts];xmin=min(b[0] for b in bounds)*coslat;xmax=max(b[2] for b in bounds)*coslat;ymin=min(b[1] for b in bounds);ymax=max(b[3] for b in bounds)
scale=min(540/(xmax-xmin),740/(ymax-ymin));ox=(600-(xmax-xmin)*scale)/2;oy=(800-(ymax-ymin)*scale)/2
def path(g,local=False):
    if local:
        a,b,c,d=g.bounds;a*=coslat;c*=coslat;factor=min(300/(c-a),300/(d-b));xx=(360-(c-a)*factor)/2;yy=(360-(d-b)*factor)/2
        point=lambda x,y:((x*coslat-a)*factor+xx,(d-y)*factor+yy)
    else:point=lambda x,y:((x*coslat-xmin)*scale+ox,(ymax-y)*scale+oy)
    rings=[]
    for poly in [g] if g.geom_type=='Polygon' else g.geoms:
        for ring in [poly.exterior,*poly.interiors]:rings.append('M'+'L'.join(f'{x:.2f},{y:.2f}' for x,y in (point(x,y) for x,y in ring.coords))+'Z')
    return ''.join(rings)
# Read the Belgrade block only: Palilula also exists in the Niš census block.
rows={}
for i,row in enumerate(openpyxl.load_workbook(inputs/'census.xlsx',read_only=True,data_only=True).active.values):
    if 7<=i<=23:rows[norm(row[2])]=(re.sub(r'\s+',' ',row[2]).strip(),int(row[1]),row[0])
assert len(rows)==17 and sum(v[1] for v in rows.values())==1681405
features=[f for f in json.loads((inputs/'ministry-4.geojson').read_text())['features'] if f['properties']['Region_DOM_ID']=='15'];assert len(features)==17
out=[]
for f in features:
    p=f['properties'];name,population,cyr=rows[norm(p['Value_sl'].removeprefix('BEOGRAD-'))];g=prep(shape(f['geometry']));center=transform(back,transform(forward,g).centroid)
    out.append({'id':'m-bg-'+norm(name).replace(' ','-'),'kind':'municipality','name':name,'aliases':[cyr,p['Value_sc'],'Beograd '+name,name+' (Beograd)'],'region':'Beograd','seat':name,'district_id':'rs-00','parent_id':'m-beograd','population':population,'year':2022,'population_source':'https://popis2022.stat.gov.rs/media/31323/0_ukupan-broj-stanovnika.xlsx','boundary_source':'https://kro.mre.gov.rs/arcgis/rest/services/IMIS/MINING_PUBLIC/MapServer/4','center':[round(center.y,7),round(center.x,7)],'path':path(g,True),'map_path':path(g),'landmark':byid['rs-00']['landmark'] if norm(name)=='stari grad' else {},'disabled':False})
out.sort(key=lambda t:t['id']);payload=json.dumps(out,ensure_ascii=False,separators=(',',':'))
(root/'srbija-na-dlanu/data/belgrade-municipalities.php').write_text("<?php\ndefined('ABSPATH') || exit;\nreturn json_decode(<<<'SND_BELGRADE'\n"+payload+"\nSND_BELGRADE\n,true);\n")
print(f'Built {len(out)} boroughs; population {sum(t["population"] for t in out):,}; {len(payload.encode()):,} bytes.')
