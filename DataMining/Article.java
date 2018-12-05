
public class Article {
	private String title;
	private String date;
	private String text;
	private String filename;
	private String url;
	
	public Article(String title, String text, String url) {
		this(title, null, text, title.replaceAll("[\\W]", "_"), url);	
	}
	
	public Article(String url, String text) {
			this(null, null, text, "" + System.currentTimeMillis(), url);
	}
	
	public Article(String title, String date, String text, String filename) {
		this(title, date, text, filename, null);
	}
	
	public Article(String title, String date, String text, String filename, String url) {
		this.title = title;
		this.date = date;
		this.text = text;
		this.filename = filename;
		this.url = url;
	}

	@Override
	public String toString() {
		return "Article [title=" + title + ", date=" + date + ", text=" + text + ", url=" + url + "]";
	}
	public String getBaseFilename(){
		return filename;
	}
	public void setFilename(String filename){
		this.filename = filename;
	}
	public void setFilenameFromTitle(){
		filename = title.replaceAll("[\\W]", "_");
	}
	public String getTitle() {
		return title;
	}
	public void setTitle(String title) {
		this.title = title;
	}
	public String getDate() {
		return date;
	}
	public String getText() {
		return text;
	}
	public void setText(String text) {
		this.text = text;
	}
	public String getUrl() {
		return url;
	}
}