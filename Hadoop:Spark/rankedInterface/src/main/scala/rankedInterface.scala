import org.apache.spark.SparkContext
import org.apache.spark.SparkContext._
import org.apache.spark.SparkConf
import org.apache.spark.rdd.RDD

import collection.immutable
import scala.runtime.ScalaRunTime._

import utilities.utility.transformText

object Interface
	{

	// number of query results
	val KVALUE = 15;

	// dictionary of word to id mapping
	var dictionary_id:immutable.Map[String, Int] = null
	
	// word id to documents containing word mapping
	var id_count:immutable.Map[Int, Int] = null

	// document id followed by the word ids with counts
	var dataset:RDD[String] = null

	// word followed by word id followed by documents containing word
	var dictionary_dataset:RDD[String] = null

	// parsed dataset with (id, list of words and counts)
	var document_set:RDD[(String, Array[(Int,Int)])] = null

	// sizes
	var document_count:Long = 0;

	def splitWordString( wordString:String ) : Array[(Int,Int)] =
		{
		return wordString.split(" ").map( x => {
							val split_string = x.split(";")
							(split_string(0).toInt, split_string(1).toInt)
						})
		}

	// get the weight of a word given a tuple of ( wordId, wordCount )
	def getWeight( value:(Int,Int), id_count_map:immutable.Map[Int, Int] ) : Double =
		{
		assert(id_count_map != null)
		if( value._2 == 0 )
			return 0
		else
			{
			val tf:Double  = (1.0 + Math.log10(value._2.toDouble))
			val idf:Double = (document_count.toDouble / id_count_map.getOrElse(value._1, -1).toDouble)
			val weight:Double = tf * idf
			return weight
			//return value._2
			}
		}

	// create equal length vectors
	def createVectors( v1:Array[(Int,Int)], v2:Array[(Int,Int)], id_count_map:immutable.Map[Int, Int] ) : (Array[Double],Array[Double]) =
		{
		assert(id_count_map != null)
		val v1_map = v1.toMap
		val v2_map = v2.toMap
		val combined = (v1.map( x => x._1 ) ++ v2.map( x => x._1 )).distinct // get a list of all words in both vectors
		val v1_out = combined.map( x => v1_map.getOrElse(x, 0).toDouble /*getWeight( (x, v1_map.getOrElse(x, 0)), id_count_map ) */ )
		val v2_out = combined.map( x => v2_map.getOrElse(x, 0).toDouble /*getWeight( (x, v2_map.getOrElse(x, 0)), id_count_map ) */)
		return (v1_out, v2_out)
		}

	// calculate the cosine similary between two vectors
	def cosineSimilarity( vectors:(Array[Double],Array[Double]) ) : Double =
		{
		val top:Double = vectors._1.zip(vectors._2).map( x => x._1 * x._2 ).sum
		val bottom_left:Double  = Math.sqrt(vectors._1.map( x => x * x).sum)
		val bottom_right:Double = Math.sqrt(vectors._2.map( x => x * x).sum)
		return top / (bottom_left * bottom_right)
		}

	// satisfy a query
	def rankedQuery(sc:SparkContext)
		{
		initialize()
		var queryString:String = Console.readLine("Enter a query string: ")
		var queryWords:Array[String] = transformText( queryString )
		println(s"Querying '$queryString'...")

		val vectorString = queryWords.map( x => dictionary_id.getOrElse(x,-1) )
					.groupBy(x => x).map(x => s"${x._1};${x._2.length}").mkString(" ")
		val queryVector  = splitWordString( vectorString )
	
		// broadcast the id_count dictionary to all of the nodes
		val b_id_count = sc.broadcast( id_count )
		assert(id_count != null)
		assert(b_id_count.value != null)

		val all_results = document_set.map( x => (x._1, cosineSimilarity(createVectors( x._2, queryVector, b_id_count.value ))) )

		val results = all_results.sortBy( x => -x._2 ).take(KVALUE).map( x => s"${x._1}(${x._2})" )

		println(s"Query '$queryString' was satisfied by documents:\n${results.mkString("\n")}")

		b_id_count.destroy()
		}

	// parse the dictionary file into word/id and id/count mappings
	def initialize()
		{
		if( dictionary_id == null || id_count == null )
			{
			val split_data_set = dictionary_dataset.map( line => line.split("\t") ).filter( x => x.length == 3 ).collect
			dictionary_id      = split_data_set.map( x => (x(0), x(1).toInt) ).toMap
			id_count           = split_data_set.map( x => (x(1).toInt, x(2).toInt) ).toMap
			document_set       = dataset.map( line => line.split("\t") )
						.filter( x => x.length == 2 )
						.map( x => (x(0), splitWordString(x(1))))
			document_count = document_set.count
			}
		}

	// ask for and satisfy the query
	def main( args: Array[String] )
		{
		var choice:Char = ' '
		var inputFile:String = " "

		// check that the correct arguments were supplied
		if( args.length < 2 )
			{
			println("usage: spark-submit program.jar [folder] [partitions]")
			System.exit(1)
			}

		// set up the spark environment
		val conf = new SparkConf().setAppName("Ranked Retrieval Interface")
		val sc = new SparkContext(conf)

		// open files
		val partitions     = args(1).toInt
		dataset            = sc.textFile(args(0) + "/ranked_index.txt", partitions)
		dictionary_dataset = sc.textFile(args(0) + "/ranked_dictionary.txt", partitions)
		
		do
			{
			println("Select Query Type (1: query, q: quit):")
			choice = Console.readChar()
			
			choice match
				{
				case '1' => rankedQuery(sc)
				case 'q' => println("Exiting")
				case _   => println("Invalid selection")
				}
			} while( choice != 'q' )

		// clean up
		sc.stop()

		}
	}