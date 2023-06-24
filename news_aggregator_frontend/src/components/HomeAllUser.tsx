import { useQuery } from 'react-query';
import NewsList from './NewsList'
import { NewsItem } from './../types/news.ts'


const HomeAllUser = () => {
    const { data, isLoading, error } = useQuery<NewsItem[]>('personalizedNews', async () => {
      const response = await fetch('http://localhost:8000/api/home-articles');
    
      if (!response.ok) {
        throw new Error('Failed to fetch home articles');
      }
    
      return response.json();
    }
  );
    
  return (
    <NewsList newsArticles={data} headline="Explore the Latest News" isLoading={isLoading} error={error as Error}/>
  )
}

export default HomeAllUser