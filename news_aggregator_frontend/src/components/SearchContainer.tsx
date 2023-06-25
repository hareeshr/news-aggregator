import React, { useEffect } from 'react';
import { useQuery } from 'react-query';
import { useLocation } from 'react-router-dom';
import NewsList from './NewsList.tsx';
import { NewsItem } from '../types/news.ts';

const SearchContainer = () => {
  const location = useLocation();

  const { data, isLoading, error, refetch } = useQuery<NewsItem[]>(
    'search',
    async () => {
      const response = await fetch(
        `http://localhost:8000/api/search${location.search}`
      );

      if (!response.ok) {
        throw new Error('Failed to fetch search results');
      }

      return response.json();
    },
    {
      enabled: !!location.search, // Enable query execution only when location.search is truthy
      refetchOnWindowFocus: false, // Disable refetch on window focus
    }
  );

  useEffect(() => {
    if (location.search) {
      refetch();
    }
  }, [location.search, refetch]);

  return (
    <NewsList
      newsArticles={data}
      headline="Search Results"
      isLoading={isLoading}
      error={error as Error}
    />
  );
};

export default SearchContainer;