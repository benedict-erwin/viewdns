#!/usr/bin/env python3
import sys, re
import urllib.request
from bs4 import BeautifulSoup
from pprint import pprint

dom = sys.argv[1]
url = 'http://viewdns.info/reverseip/?host=' + dom + '&t=1'
req = urllib.request.Request(
    url, 
    data=None, 
    headers={
        'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.47 Safari/537.36'
    }
)
res = urllib.request.urlopen(req)
par = BeautifulSoup(res, 'html.parser')
tbl = par.findAll('table')[3]
arr = []

#skip first table row
for idx, t_row in enumerate(tbl.select("tr")):
	if idx > 0:
		cells = t_row.findAll('td')
		arr.append(cells[0].get_text())

arr = list(set(arr))
pprint(arr) 