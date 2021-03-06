import org.apache.spark.SparkContext
import org.apache.spark.SparkContext._
import org.apache.spark.SparkConf
import org.apache.spark.rdd.RDD
import scala.runtime.ScalaRunTime._
import scala.xml.XML

import utilities.utility.transformText

object CreateRanked {

	def getData( sourceString:String, elementTags:Array[String] ) : Array[String] =
		{
		val document = XML.loadString(sourceString)  // load the text into an xml tree
		var text:Array[String] = Array.ofDim[String](elementTags.length + 1) // create a return array
		text(0) = (document \\ "newsitem" \ "@itemid")(0).text // get the id out of the xml
		for( i <- 1 to elementTags.length )  // get each requested element from the xml
			{
			text(i) = (document \\ elementTags(i-1))(0).text // get the element requested
			}

		return text // return an array of the requested elements
		}

	def createRankedMatrix(dataset:RDD[(String,String)], outputFilename:String)
		{
		println("creating a ranked retrieval index")

		// create a dictionary of all the document terms
		val word_list = dataset.flatMap( x => 
				{  // x._2 == file text
				val record:Array[String] = 
				    		getData( x._2, Array[String]("text") )
				transformText(record(1)).distinct
				} )

		// associate each word with an id and a document frequency
		val zipped_map = word_list.map(x => (x, 1)).reduceByKey((a,b) => a + b).zipWithIndex

		// save the dictionary to a file with format (word wordId documentsContainingWord)
		zipped_map.map( x => s"${x._1._1}\t${x._2}\t${x._1._2}" ).saveAsTextFile(outputFilename + "_dictionary")

		// make a shared dictionary that does not contain the document frequency
		val shared_dictionary = zipped_map.collect.map( x => (x._1._1, x._2)).toMap
		
		// create a ranked retrieval index and save to a text file
		val mapped_data = dataset.map( x => 
				{  // x._2 == file text
				val record:Array[String] = 
				    		getData( x._2, Array[String]("text") )
				val wordString = transformText(record(1))
						.map( x => shared_dictionary.getOrElse(x,-1))
						.groupBy(x => x)
						.map(x => s"${x._1};${x._2.length}").mkString(" ")
				record(0) + "\t" + wordString
				} ).saveAsTextFile(outputFilename + "_index")
		println("done creating a ranked	retrieval index")	      
		
		}

	def main(args: Array[String]) {
		println("\n\n\n\n")

		// check that the correct arguments were supplied
		if( args.length < 3 )
			{
			println("usage: spark-submit program.jar [folder] [outputfile] [partitions]")
			System.exit(1)
			}
		val inputFolderName = args(0)
		val outputFilename  = args(1)
		val partitions      = args(2).toInt

		// set up the spark environment
		val conf = new SparkConf().setAppName(s"Create Ranked Retrieval Index")
		val sc = new SparkContext(conf)
		val data = sc.wholeTextFiles(inputFolderName, partitions)

		// print the number of files
		println(s"There are ${data.count} files")

		// create the ranked retrieval index
		createRankedMatrix(data, outputFilename)

		// clean up
		sc.stop()

		println("\n\n\n\n")
	}
}

