import { createContext, useContext } from 'react';
import { useQuery } from 'react-query';
import { categoryItem } from './../types/news'
import { API_BASE_URL } from './../config/api';

type DataContextType = {
  categories: {
    NewsAPI: categoryItem[],
    NyTimes: categoryItem[],
    Guardian: categoryItem[],
  }
  isLoadingData: boolean
}

const DataContext = createContext<DataContextType>({
  categories: {
    NewsAPI: [],
    NyTimes: [],
    Guardian: [],
  },
  isLoadingData: true
});

export function useData() {
  return useContext(DataContext);
}

export function DataProvider({ children }: { children:React.ReactNode}) {
  // Fetch initial data from the API
  const { data: initialData, isLoading:isLoadingData } = useQuery('data', async () => {
    const response = await fetch(`${API_BASE_URL}/categories`);
    if (!response.ok) {
      throw new Error('Failed to fetch categories');
    }
    return response.json();
  });

  const categories = {
      NewsAPI: initialData?.NewsAPI || [],
      NyTimes: initialData?.NyTimes || [],
      Guardian: initialData?.Guardian || [],
  };

  return (
    <DataContext.Provider value={{categories, isLoadingData }}>
      {children}
    </DataContext.Provider>
  );
}
