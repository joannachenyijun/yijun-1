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

with open(inputFile, 'r') as inFile:
	with open(outputFile,'w') as outFile:
		crimereader = csv.reader(inFile, delimiter='\t')
		crimewriter = csv.writer(outFile, delimiter='\t', lineterminator="\n")

#0ID,1Case Number,2Date,3Block,4IUCR,5Primary Type,6Description,7Location Description,
#8Arrest,9Domestic,10Beat,11District,12Ward,13Community Area,14FBI Code,
#15X Coordinate,16Y Coordinate,17Year,18Updated On,19Latitude,20Longitude,21Location
		rowsToGet = [0, 3, 2, 4, 15, 16, 19, 20]
		crimewriter.writerow(["#ID", "block", "date", "IUCR", "X", "Y", "Latitude", "Longitude"])
		i = 0
		for row in crimereader:
			i+=1
			newrow = [row[x] for x in rowsToGet]
			crimewriter.writerow(newrow)
			if i % 100000 == 0:
				printProgress( i, '?' )
				
