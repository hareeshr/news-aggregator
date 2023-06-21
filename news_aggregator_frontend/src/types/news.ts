
export interface NewsItem {
    id: string;
    source: string;
    title: string;
    author: string | null;
    publishedAt: string;
    url: string;
}