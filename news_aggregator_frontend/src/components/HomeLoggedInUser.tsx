import { useQuery } from 'react-query';
import NewsList from './NewsList.tsx'
import { NewsItem } from '../types/news.ts'


const HomeLoggedInUser = () => {
    const token = localStorage.getItem('token');
    const { data, isLoading, error } = useQuery<NewsItem[]>('personalizedNews', async () => {
        const response = await fetch('http://localhost:8000/api/getPersonalizedArticles', {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
      
        if (!response.ok) {
          throw new Error('Failed to fetch user data');
        }
      
        return response.json();
      }
    );
    
  return (
    <NewsList newsArticles={data} headline="Explore your Personalized Feed" isLoading={isLoading} error={error as Error}/>
  )
}

export default HomeLoggedInUser