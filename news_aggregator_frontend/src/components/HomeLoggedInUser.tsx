import React from 'react';
import { useQuery } from 'react-query';
import NewsList from './NewsList';
import { NewsItem } from '../types/news';
import { API_BASE_URL } from './../config/api';

const HomeLoggedInUser: React.FC = () => {
  const token = localStorage.getItem('token');
  const { data, isLoading, error } = useQuery<NewsItem[]>('personalizedNews', async () => {
    const response = await fetch(`${API_BASE_URL}/personalized-articles`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    if (!response.ok) {
      throw new Error('Failed to fetch personalized articles');
    }

    return response.json();
  });

  return (
    <NewsList
      newsArticles={data}
      headline="Explore your Personalized Feed"
      isLoading={isLoading}
      error={error as Error}
    />
  );
};

export default HomeLoggedInUser;
