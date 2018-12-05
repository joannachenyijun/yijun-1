import sys
import csv

# display current progress
def printProgress( current, count ):
	sys.stdout.write("\r" + str(current) + "/" + str(count))
	sys.stdout.flush()

if len(sys.argv) < 3:
	print("usage: " + sys.argv[0] +" inputFile outputFile")
	exit()
	
inputFile = sys.argv[1]
outputFile = sys.argv[2]

fileLUT = {}
def setupTables(base_file):
	global fileLUT
	for i in range(2001, 2017):
		fileLUT[str(i)] = open("{}_{}.csv".format(base_file, i),'w')

def cleanTable():
	global fileLUT
	for f in fileLUT.values():
		f.close()
	fileLUT = {}


setupTables( outputFile )

# get file length
end = 0
with open(inputFile, 'r') as inFile:
	for line in inFile:
		end += 1

# divide by year
cur_year = ""
year     = ""
with open(inputFile, 'r') as inFile:
		crimereader = csv.reader(inFile)
#0ID,1Case Number,2Date,3Block,4IUCR,5Primary Type,6Description,7Location Description,
#8Arrest,9Domestic,10Beat,11District,12Ward,13Community Area,14FBI Code,
#15X Coordinate,16Y Coordinate,17Year,18Updated On,19Latitude,20Longitude,21Location
		i = 0
		for row in crimereader:
			try:
				year = row[17]
				if year != cur_year:
					crimewriter = csv.writer(fileLUT[year], delimiter='\t', lineterminator="\n")
					cur_year = year
				crimewriter.writerow( row )
			except:
				pass
			i+=1
			if i % 100000 == 0:
				printProgress( i, end )
				
cleanTable()
