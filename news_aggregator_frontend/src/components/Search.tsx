import { ArrowRightCircleIcon, MagnifyingGlassIcon } from "@heroicons/react/24/solid";
import { useState, useRef, useEffect } from "react";
import { useNavigate } from 'react-router-dom';
import NewsSourceFilter from "./NewsSourceFilter";
import { categoryItem } from "../types/news";
import Multiselect from 'multiselect-react-dropdown';
import { useData } from "../context/DataContext";
import { useQueryClient } from 'react-query';

type FormData = {
  q: string,
  NewsAPI?: string;
  NewsAPICategories?: string;
  NyTimes?: string;
  NyTimesCategories?: string;
  Guardian?: string;
  GuardianCategories?: string;
  startDate?: string;
  endDate?: string;
}

function Search() {
  // const [searchInput, setSearchInput] = useState('');
  const searchInputRef = useRef<HTMLInputElement>(null);
  
  const [startDate, setStartDate] = useState('');
  const [endDate, setEndDate] = useState('');
  const [isFocused, setIsFocused] = useState(false);
  const formRef = useRef<HTMLFormElement>(null);

  const [NewsAPI, setNewsAPI] = useState(false);
  const [NewsAPIOptions, setNewsAPIOptions] = useState<categoryItem[]>([]);
  const [NyTimes, setNyTimes] = useState(false);
  const [NyTimesOptions, setNyTimesOptions] = useState<categoryItem[]>([]);
  const [Guardian, setGuardian] = useState(false);
  const [GuardianOptions, setGuardianOptions] = useState<categoryItem[]>([]);

  const NewsAPIRef = useRef<Multiselect | null>(null);
  const NyTimesRef = useRef<Multiselect | null>(null);
  const GuardianRef = useRef<Multiselect | null>(null);

  const queryClient = useQueryClient();

  const { categories, isLoadingData } = useData();
  const navigate = useNavigate();

  const handleStartDateChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setStartDate(e.target.value);
  };

  const handleEndDateChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setEndDate(e.target.value);
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    queryClient.invalidateQueries('search');

    const formData: FormData = {
      q: searchInputRef.current?.value || ''
    }

    if(startDate) formData.startDate = startDate;
    if(endDate) formData.endDate = endDate;

    if(NewsAPI) formData.NewsAPI = "true";
    let NewsAPICategories = NewsAPIRef.current?.getSelectedItems();
    if(NewsAPICategories && NewsAPICategories.length > 0 ) formData.NewsAPICategories = NewsAPICategories.map((item:categoryItem) => item.key).join(",") || "";
    
    if(NyTimes) formData.NyTimes = "true";
    let NyTimesCategories = NyTimesRef.current?.getSelectedItems();
    if(NyTimesCategories && NyTimesCategories.length > 0 ) formData.NyTimesCategories = NyTimesCategories.map((item:categoryItem) => item.key).join(",") || "";

    if(Guardian) formData.Guardian = "true";
    let GuardianCategories = GuardianRef.current?.getSelectedItems();
    if(GuardianCategories && GuardianCategories.length > 0 ) formData.GuardianCategories = GuardianCategories.map((item:categoryItem) => item.key).join(",") || "";

    const queryParams = new URLSearchParams(formData);
    const queryString = queryParams.toString();
    console.log(queryString);
    navigate(`/search?${queryString}`);

    handleCloseFocused();
  };

  const handleCloseFocused = () => {
    setIsFocused(false);
    
    setNewsAPIOptions(NewsAPIRef.current?.getSelectedItems() || []);
    setNyTimesOptions(NyTimesRef.current?.getSelectedItems() || []);
    setGuardianOptions(GuardianRef.current?.getSelectedItems() || []);
  }

  useEffect(() => {
    const handleClickOutside = (e: MouseEvent) => {
      if (formRef.current && !formRef.current.contains(e.target as Node)) {
        handleCloseFocused();
      }
    };

    document.addEventListener('mousedown', handleClickOutside);

    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, []);

  

  return (
    <form onSubmit={handleSubmit}
      className="relative"
      ref={formRef}>

      <MagnifyingGlassIcon className="absolute w-6 h-6 text-gray-400 top-2.5 left-2" />

      <input
        type="text"
        placeholder="Search"
        className="border-solid border-2 border-gray-300 p-2 w-full text-left pl-10"
        onFocus={() => setIsFocused(true)}
        ref={searchInputRef}
      />


      {isFocused && (
        <div className="advancedFilters absolute border bg-white w-full p-3 flex flex-col gap-2 z-10">

          {/* NewsAPI Options */}
          <NewsSourceFilter 
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
          <NewsSourceFilter 
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
          <NewsSourceFilter 
              source={Guardian}
              setSouce={setGuardian}
              isLoadingData={isLoadingData}
              categories={categories.Guardian}
              id="GuardianCheckbox"
              name="Guardian"
              selectedValues={GuardianOptions}
              sourceRef={GuardianRef}
              />

          <div className="mt-2">
            <label htmlFor="startDate">Start Date:</label>
            <input
              type="date"
              id="startDate"
              value={startDate}
              onChange={handleStartDateChange}
            />
          </div>

          <div className="mt-2">
            <label htmlFor="endDate">End Date:</label>
            <input
              type="date"
              id="endDate"
              value={endDate}
              onChange={handleEndDateChange}
            />
          </div>

        </div>
      )}

      <button type="submit" className="top-0.5 right-1 absolute">
        <ArrowRightCircleIcon className="w-10 h-10 text-gray-800"/>
      </button>
    </form>
  );
}

export default Search