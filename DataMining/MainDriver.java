

public class MainDriver {
	public static void main(String[] args) throws Exception{
		// set path of where to save html files and text
		//String path = "C:\\Users\\x\\Documents\\Spring 2016\\865\\groupProject\\articles\\";
		String path = "C:\\Users\\x\\articles\\";
		
		// download all OANN news articles and convert to text
		OANN downloader = new OANN();
		downloader.setUSVars();
		downloader.downloadAllArticles(path);
		System.out.println("Done");
	}
}