
export type NewsItem = {
    id: string;
    source: string;
    title: string;
    author: string | null;
    publishedAt: string;
    url: string;
}

export type categoryItem = {
    key: string,
    name: string
}