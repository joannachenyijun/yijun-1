package utilities

package object utility {
	
	def transformText( input:String ) : Array[String] = 
		{
		return input.split("\\s+").map( x => fixString( x ) )
		}

	def fixString( input:String ) : String =
		{
		return input.trim().toLowerCase().replaceAll("^[^a-z0-9]+", "").replaceAll("[^a-z0-9]+$", "")
		}
  }
