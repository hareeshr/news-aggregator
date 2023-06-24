import React from 'react';
import NewsArticle from './NewsArticle';
import { NewsItem } from '../types/news';

type NewsListProps = {
  newsArticles: NewsItem[] | undefined;
  headline: string;
  isLoading: boolean;
  error: Error;
};

const NewsList: React.FC<NewsListProps> = ({ newsArticles, headline, isLoading, error }) => {
  return (
    <div>
      <h1 className="text-3xl pb-10">{headline}</h1>
      {isLoading ? (
        <div>Loading...</div>
      ) : newsArticles?.length! > 0 ? (
        <ul className="grid grid-cols-1 gap-x-5 gap-y-10 md:grid-cols-2">
          {newsArticles!.map((newsItem, index) => (
            <NewsArticle key={index + newsItem.id} newsItem={newsItem} />
          ))}
        </ul>
      ) : (
        <div>No updates available at the moment.</div>
      )}
      {error && <div>Error: {error.message || 'An error occurred'}</div>}
    </div>
  );
};

export default NewsList;
