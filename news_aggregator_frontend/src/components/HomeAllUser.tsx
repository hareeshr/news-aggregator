import { useQuery } from 'react-query';
import NewsList from './NewsList'
import { NewsItem } from './../types/news.ts'


const HomeAllUser = () => {
    const { data, isLoading, error } = useQuery<NewsItem[]>('news', () =>
        fetch('http://localhost:8000/api/home').then((response) => response.json())
    );
    
  return (
    <NewsList newsArticles={data} headline="Explore the Latest News" isLoading={isLoading} error={error as Error}/>
  )
}

export default HomeAllUser