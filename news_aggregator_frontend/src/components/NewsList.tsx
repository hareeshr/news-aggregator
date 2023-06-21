import NewsArticle from './NewsArticle.tsx'
import { NewsItem } from './../types/news.ts'

type NewsListProps = {
    newsArticles: NewsItem[] | undefined,
    headline: string,
    isLoading: boolean,
    error: Error
}

const NewsList = ({ newsArticles, headline, isLoading, error } : NewsListProps) => {
    
  return (
    <div>
        <h1 className="text-3xl pb-10">{headline}</h1>
        {isLoading
            ? <div>Loading...</div>
            : (
                <ul className="grid grid-cols-2 gap-x-3 gap-y-10">
                    {newsArticles?.map((newsItem) => (
                        <NewsArticle key={newsItem.id} newsItem={newsItem} />
                    ))}
                </ul>
            )
        }
        {error && (<div>Error: {error ? error.message : 'An error occurred'}</div>)}
    </div>
  )
}

export default NewsList