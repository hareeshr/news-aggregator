import { XMarkIcon, CubeTransparentIcon } from '@heroicons/react/24/solid'
import { useMutation, useQuery, useQueryClient } from 'react-query';
import { useData } from './../context/DataContext';
import { useState, useEffect, useRef } from 'react'
import UserData from './UserData';
import PreferenceSource from './PreferenceSource';
import { categoryItem } from './../types/news'
import Multiselect from 'multiselect-react-dropdown';
import { toast } from 'react-toastify';


type PreferenceData = {
    NewsAPI: boolean,
    NewsAPICategories: categoryItem[],
    NyTimes: boolean,
    NyTimesCategories: categoryItem[],
    Guardian: boolean,
    GuardianCategories: categoryItem[],
}

type SidePaneProps = {
    toggleSidePane: () => void
}

const fetchUserPreferences = async (token: string): Promise<PreferenceData> => {
  const response = await fetch('http://localhost:8000/api/user/preferences', {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  });

  if (!response.ok) {
    throw new Error('Failed to fetch user data');
  }

  return response.json();
};

const useSaveUserPreferences = (token: string) => {
  const [isLoadingSave, setIsLoadingSave] = useState(false);
  const queryClient = useQueryClient();

  const mutation = useMutation(
    async (formData: PreferenceData) => {
      setIsLoadingSave(true);
      const response = await fetch('http://localhost:8000/api/user/preferences', {
        method: 'POST',
        body: JSON.stringify(formData),
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
        },
      });

      if (!response.ok) {
        throw new Error('Failed to save preferences.');
      }

      setIsLoadingSave(false);
      return response.json();
    },
    {
      onSuccess: () => {
        toast.success('Preferences Saved Successfully.');
        queryClient.invalidateQueries('personalizedNews'); // Invalidates the 'news' query to trigger a refetch
      },
      onError: () => {
        toast.error('Failed to save preferences.');
      },
    }
  );

  return {
    mutation,
    isLoadingSave,
  };
};

const SidePane = ({toggleSidePane}: SidePaneProps) => {
  const token = localStorage.getItem('token');
  const {categories, isLoadingData} = useData();

  const [NewsAPI, setNewsAPI] = useState(false);
  const [NewsAPIOptions, setNewsAPIOptions] = useState<categoryItem[]>([]);
  const [NyTimes, setNyTimes] = useState(false);
  const [NyTimesOptions, setNyTimesOptions] = useState<categoryItem[]>([]);
  const [Guardian, setGuardian] = useState(false);
  const [GuardianOptions, setGuardianOptions] = useState<categoryItem[]>([]);

  const NewsAPIRef = useRef<Multiselect | null>(null);
  const NyTimesRef = useRef<Multiselect | null>(null);
  const GuardianRef = useRef<Multiselect | null>(null);

  const { data: preferences, isLoading } = useQuery<PreferenceData>('PreferenceData', () =>
  fetchUserPreferences(token || '')
  );

  useEffect(() => {
    if(!isLoading) {
        setNewsAPI(preferences?.NewsAPI || false);
        setNewsAPIOptions(preferences?.NewsAPICategories || []);
        setNyTimes(preferences?.NyTimes || false);
        setNyTimesOptions(preferences?.NyTimesCategories || []);
        setGuardian(preferences?.Guardian || false);
        setGuardianOptions(preferences?.GuardianCategories || []);
    }
  }, [preferences]);

  const { mutation, isLoadingSave } = useSaveUserPreferences(token || '');

  const handleSubmit = (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    
    const formData = {
        NewsAPI: NewsAPI,
        NewsAPICategories: NewsAPIRef.current?.getSelectedItems(),
        NyTimes: NyTimes,
        NyTimesCategories: NyTimesRef.current?.getSelectedItems(),
        Guardian: Guardian,
        GuardianCategories: GuardianRef.current?.getSelectedItems(),
    }

    mutation.mutate(formData);
  };

  return (
    <div className="fixed top-0 right-0 w-[20rem] h-full bg-gray-300 p-10 z-10">
        <div className="flex flex-col items-center">
            <button className="absolute top-5 right-5 text-gray-800" onClick={toggleSidePane}>
                <XMarkIcon className="w-8 h-8"/>
            </button>
            {!isLoading && !isLoadingData 
                ?
                <>
                    <UserData />

                    <h1 className="w-full mt-8 font-medium">Feed Preferences</h1>
                    <form className="w-full" onSubmit={handleSubmit}>
                        
                        {/* NewsAPI Options */}
                        <PreferenceSource 
                            source={NewsAPI}
                            setSouce={setNewsAPI}
                            isLoadingData={isLoadingData}
                            categories={categories.NewsAPI}
                            id="NewsAPICheckbox"
                            name="NewsAPI"
                            selectedValues={NewsAPIOptions}
                            sourceRef={NewsAPIRef}
                            singleSelect={true}
                            />
                        
                        {/* New York Times Options */}
                        <PreferenceSource 
                            source={NyTimes}
                            setSouce={setNyTimes}
                            isLoadingData={isLoadingData}
                            categories={categories.NyTimes}
                            id="NyTimesCheckbox"
                            name="New York Times"
                            selectedValues={NyTimesOptions}
                            sourceRef={NyTimesRef}
                            />
                        
                        {/* Guardian Options */}
                        <PreferenceSource 
                            source={Guardian}
                            setSouce={setGuardian}
                            isLoadingData={isLoadingData}
                            categories={categories.Guardian}
                            id="GuardianCheckbox"
                            name="Guardian"
                            selectedValues={GuardianOptions}
                            sourceRef={GuardianRef}
                            />

                        <div className="flex items-end">
                            <button type="submit" className="bg-gray-700 text-white px-2 py-1 mt-5" disabled={isLoadingSave}>Save</button>
                            {isLoadingSave && <CubeTransparentIcon className="w-5 h-5 m-1.5 animate-spin" />}
                        </div>
                    </form>
                </>
                : <div>Loading...</div>
            }
        </div>
    </div>
  )
}

export default SidePane