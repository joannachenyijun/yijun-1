import sys
import os
import re
from nltk.stem import WordNetLemmatizer

# display current progress
def printProgress( current, count ):
	sys.stdout.write("\r" + str(current) + "/" + str(count))
	sys.stdout.flush()

if len(sys.argv) < 3:
	print("usage: " + sys.argv[0] +" inputFolder outputFile")
	print(sys.argv)
	exit()
	
inputFolder = sys.argv[1]
outputFileName = sys.argv[2]

# read list of stopwords
stopWords = {}
with open('stop-word-list.txt', 'r') as inFile:
	for stopword in inFile:
		stopWords[stopword.rstrip()] = ""

# process all files
files = os.listdir(inputFolder)
numFiles = len(files)
i = 0
outputLines = []
for fileName in files:
	try:
		inFile = open(inputFolder + fileName, 'r')
		title = re.search(r"(.*?)\-\-", fileName)
		time = re.search(r"\-\-(.*?20[0-9][0-9])\.", fileName)
		if time == None or title == None:
			continue
		title = title.group(1).lstrip('_').rstrip('_')
		time = time.group(1)
		
		outText = []
		for line in inFile:
			for word in line.split():
				for wordPart in re.split("[_\-\/]", word):
					outWord = re.sub("[^a-z0-9]","", wordPart.lower())
					if len(outWord) > 1 and outWord not in stopWords:
						outText.append( outWord )
		inFile.close()
		outputLines.append([title, time, outText])
		i += 1
		printProgress( i, numFiles )

	except:
		print("Error with" + fileName)

# get the base form of a word
def stemWord( word):
	wordnet_lemmatizer = WordNetLemmatizer()
	nword = wordnet_lemmatizer.lemmatize(word, pos='n')
	vword = wordnet_lemmatizer.lemmatize(word, pos='v')
	adjword = wordnet_lemmatizer.lemmatize(word, pos='a')
	adj2word = wordnet_lemmatizer.lemmatize(word, pos='s')
	advword = wordnet_lemmatizer.lemmatize(word, pos='r')
	
	if word != vword:
		rWord = vword
	elif word != nword:
		rWord = nword
	elif word != advword:
		rWord = advword
	elif word != adjword:
		rWord = adjword
	elif word != adj2word:
		rWord = adj2word
	else:
		rWord = word
	return rWord

# stem all words in a list
def stemText(text):
	newLine = ""
	for word in text:
		newLine += stemWord(word) + " "
	return newLine.rstrip()

# create an article index file
numFiles = i
print("")
with open(outputFileName,'w') as outFile:
	i = 0
	for line in outputLines:
		if line[2][0] == "search":
			del line[2][0]
		text = line[1] +"\t" + line[0] + "\t" + stemText(line[2]) + "\n"
		outFile.write(text)
		i += 1
		printProgress( i, numFiles)