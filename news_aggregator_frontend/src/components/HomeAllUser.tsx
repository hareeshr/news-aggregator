import React from 'react';
import { useQuery } from 'react-query';
import NewsList from './NewsList';
import { NewsItem } from './../types/news.ts';
import { API_BASE_URL } from './../config/api';

const HomeAllUser: React.FC = () => {
  const { data, isLoading, error } = useQuery<NewsItem[]>('personalizedNews', async () => {
    const response = await fetch(`${API_BASE_URL}/home-articles`);

    if (!response.ok) {
      throw new Error('Failed to fetch home articles');
    }

    return response.json();
  });

  return (
    <NewsList
      newsArticles={data}
      headline="Explore the Latest News"
      isLoading={isLoading}
      error={error as Error}
    />
  );
};

export default HomeAllUser;
