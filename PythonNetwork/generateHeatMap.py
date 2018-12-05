from __future__ import print_function
from pyproj import Proj, transform
from PIL import Image
from decimal import *
import sys
import csv
import math

inputFile = sys.argv[1]

squareImageSize = 1280

#This is how much the image should be shifted from the map tiling boundaries
scale = 39135.75848201*1.2375

#These are the boundaries of the image according to Google Maps' projection
bottomLat = 5135772.28 - scale
topLat = 5135772.28 + scale
intervalLat = (topLat - bottomLat)/squareImageSize

leftLong = -9765669.31 - scale
rightLong = -9765669.31 + scale
intervalLong = (rightLong - leftLong)/squareImageSize

# NAD 1983 StatePlane Illinois East FIPS 1201 Feet 
# ESPG: 102671
# inProj = Proj('+proj=tmerc +lat_0=36.66666666666666 +lon_0=-88.33333333333333 ' +
# 	'k=0.999975 +x_0=300000 +y_0=0 +ellps=GRS80 +datum=NAD83 '
#  	'+to_meter=0.3048006096012192 no_defs', preserve_units=True)
#inProj2 = inProj.to_latlong()

# Regular latitude and longitude used in GPS
# ESPG: 4326
#inProj = Proj('+proj=longlat +datum=WGS84 +no_defs')
inProj = Proj(init='epsg:4326')
# Google maps projection
# ESPG: 3857
# outProj = Proj('+proj=merc +a=6378137 +b=6378137 +lat_ts=0.0 +lon_0=0.0 +x_0=0.0' + 
# ' +y_0=0 +k=1.0 +units=m +nadgrids=@null +wktext  +no_defs')
outProj = Proj(init='epsg:3857')

heatmap = [[0 for i in range(squareImageSize)] for j in range(squareImageSize)]

with open(inputFile, 'r') as inFile:
	#Use \t as delimiter if file was never opened in something like Excel, else use a comma
	crimereader = csv.reader(inFile, delimiter='\t')
	crimereader.next()
	rowsToGet = [6, 7]
	for row in crimereader:
		#print(len(row))
		#print(row)
 		if (row[6] != "") and (row[7] != ""):
			newRow = [row[x] for x in rowsToGet]
			#print(newRow)
			#print(row)
			#print(int(newRow[0]))
  			x, y = transform(inProj, outProj, newRow[1], newRow[0])
			#x, y = transform(secProj, outProj, x1, y1)
 			#print("%f %f" % (x, y))
			lat = (topLat - y) / intervalLat
			long = (rightLong - x) / intervalLong
			#print("%f %f" % (lat, long))
			if(int(math.floor(long)) <= 1280):
				if(int(math.floor(long)) <= 1280):
					if(int(math.floor(long)) >= 0):
						if(int(math.floor(long)) >= 0):
							heatmap[int(math.floor(long))][int(math.floor(lat))] += 1
			# if (float(newRow[1]) < 41.977795) and (float(newRow[0]) < -87.639942):
#  				print("%s %s %s %s %s %f %f" % (newRow[0], newRow[1], row[4], row[5], row[1], lat, long))
 				#heatmap[int(math.floor(long))][int(math.floor(lat))] -= 2

original = Image.open("Chicago 41.8315,-87.7265.jpeg")
im = original.copy()
# im.convert('RGB')
pix = im.load()
#print(im.mode)
for i in range(im.size[0]):
	for j in range(im.size[1]):
		if heatmap[i][j] > 0:
			pix[squareImageSize - 1 - i, j] = (0, 0, 255)
		if heatmap[i][j] > 10:
			pix[squareImageSize - 1 - i, j] = (255, 0, 0)
		# if heatmap[i][j] < 0:
# 			pix[squareImageSize - 1 - i, j] = (75, 130, 0)


# print(pix[200,200])
im.save(sys.argv[2])
#im.show()