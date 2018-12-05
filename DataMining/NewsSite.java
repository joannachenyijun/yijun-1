import java.io.BufferedOutputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.OutputStream;
import java.net.URL;
import java.util.LinkedList;

import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import de.l3s.boilerpipe.extractors.ArticleExtractor;

public class NewsSite implements NewsSiteInterface{
	
	private String url;
	
	public NewsSite(String url)
	{
		this.url = url;
	}
	
	public void downloadAllArticles(String path, int pages, boolean parse)
	{
		System.out.println("I shouldn't be here");
		System.exit(1);
	}
	
	public Article getArticle(String text) {
		System.out.println("I shouldn't be here");
		System.exit(1);
		return null;
	}
	
	public LinkedList<Article> getURLs( String urlString ){
		System.out.println("I shouldn't be here");
		System.exit(1);
		return null;
	}
	
	// make an article based from file contents
	private Article getArticle(String filename, String text){
		return new Article( filename, null, text, filename, null);
	}
	
	// get single news article text
	public String fetchArticle( String urlString) throws Exception {
		URL url = new URL(urlString);
		String text = ArticleExtractor.INSTANCE.getText(url);
		return text;
	}
	
	// download articles on a page
	public int downloadPage(String path, String pageURL, String containerTag, boolean parse){		
		LinkedList<Article> articles;
		int downloadCount = 0;
		articles = getURLs(pageURL);
		downloadArticles(articles);
		saveFiles(path, articles, false);
		downloadCount += articles.size();
		if( parse )
		{
			parseHTML( path, articles );
		}
		return downloadCount;
	}	
	
	// download webpage without extracting article
	public void downloadArticles(LinkedList<Article> urls){
		downloadArticles(urls, false);
	}
	
	// download webpage and extract article text if specified
	public void downloadArticles(LinkedList<Article> urls, boolean extractArticle)
	{
		for( Article article : urls )
		{
			try{
				Document doc = Jsoup.connect(article.getUrl()).get();
				String title = doc.title();
				String text;
				text = (extractArticle? ArticleExtractor.INSTANCE.getText(doc.html()) : doc.html());
				article.setTitle(title);
				article.setText(text);
				article.setFilenameFromTitle();
			}
			catch(Exception e) {
				System.out.println("Error Downloading: " + article.getUrl());
			}
		}
	}
	
	// parse list of html articles
	public void parseHTML( String path, LinkedList<Article> htmlArticles ){

		LinkedList<Article> articles = new LinkedList<Article>();
		String text;
		Article article;
		for (Article htmlArticle : htmlArticles) {
				try{
					text = ArticleExtractor.INSTANCE.getText(htmlArticle.getText());
					article = getArticle(text);
					articles.add(article);
				}
				catch(Exception e) {
					System.out.println("Error with " + htmlArticle.getTitle() + " from " + htmlArticle.getUrl() + "\n");
				}
		    }
	    saveFiles(path, articles, true);
	    System.out.println("Parsed " + articles.size() + " articles from " + this.url);
	}
	
	// save a list of articles
	public void saveFiles( String destination, LinkedList<Article> articles, boolean isTXT){
        String fileName = destination;
        int i = 0;
        String ext = isTXT? ".txt" : ".html";
        OutputStream out;
		for( Article article : articles)
		{
	        fileName = destination + article.getBaseFilename() + ext;
			try {
				out = new BufferedOutputStream(new FileOutputStream(fileName));
		        out.write(article.getText().getBytes());
		        out.close();
			} catch (Exception e) {
				System.out.println(i + " Error Saving: " + article.getTitle() + "\nwith title: " + article.getBaseFilename() + "\nfrom: " + article.getUrl() + "\n" + e.toString());
			}
	        i++;
		}
	}
	
	// parse html for articles already on disk
	public void parseHTMLfromDirectory( String path ){
		File[] files = new File(path).listFiles();

		LinkedList<Article> articles = new LinkedList<Article>();
		String text;
		Article article;
		for (File file : files) {
		    if (file.getName().contains(".html")) {
				try{
					text = ArticleExtractor.INSTANCE.getText(new FileReader(file));
					article = getArticle(file.getName().replace(".html", ".txt"), text);
					articles.add(article);
				}
				catch(Exception e) {
					System.out.println("Error with " + file.getName() + "\n");
				}
		    }
		}
	    saveFiles(path, articles, true);
	    System.out.println("Parsed " + articles.size() + " articles from " + path);
	}
}