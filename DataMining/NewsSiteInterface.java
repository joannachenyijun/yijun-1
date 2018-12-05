import java.util.LinkedList;

public interface NewsSiteInterface {
	// download all pages on a news site
	public void downloadAllArticles(String path, int pages, boolean parse);
	
	//extract Article details from html
	public Article getArticle(String text);
	
	// parse out URLs for news articles on a given page referenced by URL
	public LinkedList<Article> getURLs( String urlString );
}