import sys
import os
import datetime
import time
import csv

def printProgress( current, count ):
	sys.stdout.write("\r" + str(current) + "/" + str(count))
	sys.stdout.flush()

if len(sys.argv) < 3:
	print("usage: " + sys.argv[0] + " inputFile" + " outputFolder")
	exit()
	
inputFileName = sys.argv[1]
outputFolder = sys.argv[2]

# print the progress
def printProgress( current, count ):
	sys.stdout.write("\r" + str(current) + "/" + str(count))
	sys.stdout.flush()

# convert the date to a usable format
def convertDate( date ):
	ndate = datetime.datetime.strptime(date, "%B %d, %Y")
	return int(time.mktime(ndate.timetuple()))

# embed the meta data
def setMetaData( filename, date ):
	mdate = convertDate( date )
	os.utime(filename,(mdate,mdate))

# create a file corresponding to each line in the tab-separated index file [date, title, content]
with open(inputFileName, 'r') as inputFile:
		i = 0
		lineReader = csv.reader(inputFile, delimiter = "\t")
		for line in lineReader:
			i += 1
			title = "doc" + str(i)
			filename = outputFolder + title + ".txt"
			outputFile =  open(filename, 'w')
			contents = line[2]
			outputFile.write(contents)
			outputFile.close()
			date = line[0]
			setMetaData(filename, date)
			printProgress( i, "?" )