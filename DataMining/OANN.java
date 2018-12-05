import java.io.IOException;
import java.util.LinkedList;

import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;

public class OANN extends NewsSite implements NewsSiteInterface{
	
	private String componentTag = "article";
	private String urlPlaceholder;
	private int pages;
	
	public OANN() {
		super("www.oann.com");
		setUSVars();
	}

	// download US News
	public void setUSVars(){
		urlPlaceholder = "http://www.oann.com/category/u-s-news/page/#/";
		pages = 147;
	}
	
	// download World News
	public void setWorldVars(){
		urlPlaceholder = "http://www.oann.com/category/world/page/#/";
		pages = 416;
	}
	
	@Override
	public Article getArticle(String text)
	{
		String[] lines = text.split("\n");
		String title = lines[1];
		String date = lines[3];
		String fileName = title.replaceAll("[\\W]", "_") + "--" + date;
		return new Article(title, date, text, fileName);
	}

	public void downloadFirstPageArticles(String path){
		downloadAllArticles(path, 2, 3, true);
	}
	
	public void downloadAllArticles(String path){
		downloadAllArticles(path, 2, pages, true);
	}
	
	// download articles
	public void downloadAllArticles(String path, int startPage, int endPage, boolean parse){
		String urlString;
		int downloadCount = 0;
		for( int i = startPage; i < endPage; i++ )
		{
			urlString = urlPlaceholder.replaceFirst("#", String.valueOf(i));
			downloadCount += downloadPage(path, urlString, componentTag, parse);
			try {
				Thread.sleep(10000);
			} catch (InterruptedException e) {
				e.printStackTrace();
			}
			System.out.println("Downloaded Page " + i);
		}
		System.out.println("\nDownloaded " + downloadCount + " articles from oann.com");
	}
	
	// parse out URLs for news articles on a given page referenced by URL
	public LinkedList<Article> getURLs( String urlString ){
		LinkedList<Article> urlList = new LinkedList<Article>();
		Document doc;
		Article article;
		try {
			doc = Jsoup.connect(urlString).get();
			Elements containers = doc.getElementsByTag(componentTag);
			for( Element container : containers ){
				article = new Article(container.getElementsByTag("a").first().attr("href"), container.html());
				urlList.add(article);
			}
		} catch (IOException e) {
			System.out.println("Unable to connect to " + urlString);
		}
		return urlList;
	}
}