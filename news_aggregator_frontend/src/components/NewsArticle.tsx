import { Link } from 'react-router-dom';
import { NewsItem } from './../types/news.ts'
import { NewspaperIcon } from '@heroicons/react/24/solid';
import TimeAgo from './TimeAgo.tsx'

type NewsArticleProps = {
    newsItem: NewsItem
}

function stripHTML(htmlString:string) {
    const temporaryElement = document.createElement('div');
    temporaryElement.innerHTML = htmlString;
    return temporaryElement.textContent || temporaryElement.innerText || '';
}

const NewsArticle = ({ newsItem } : NewsArticleProps) => {
  return (
    <li className="border-l-4 border-gray-800 pl-3 hover:translate-x-1 transition-transform flex">
        <Link to={newsItem.url} target="_blank" rel="noopener noreferrer" className="flex flex-col justify-between content-between w-full">
            
            <h2>{stripHTML(newsItem.title)}</h2>
            <div className="flex gap-2 items-center py-2">
                <NewspaperIcon className="text-gray-800 w-8 h-8" />
                <div className="text-sm flex-grow">
                    <div className="font-medium">{stripHTML(newsItem.source)}</div>
                    {newsItem.author && <div>- {stripHTML(newsItem.author)}</div>}
                </div>
                <div className="bg-gray-800 text-white inline-block py-1 px-3 rounded-full text-xs">
                    <TimeAgo timestamp={newsItem.publishedAt}/>
                </div>
            </div>
        </Link>
    </li>
  )
}

export default NewsArticle