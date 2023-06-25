import React, { useState, useRef, useEffect } from "react";
import { ArrowRightCircleIcon, MagnifyingGlassIcon, XMarkIcon } from "@heroicons/react/24/solid";
import { useNavigate } from 'react-router-dom';
import { toast } from 'react-toastify';
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
  const searchInputRef = useRef<HTMLInputElement>(null);
  const formRef = useRef<HTMLFormElement>(null);
  const NewsAPIRef = useRef<Multiselect | null>(null);
  const NyTimesRef = useRef<Multiselect | null>(null);
  const GuardianRef = useRef<Multiselect | null>(null);
  const queryClient = useQueryClient();
  const { categories, isLoadingData } = useData();
  const navigate = useNavigate();

  const [startDate, setStartDate] = useState('');
  const [endDate, setEndDate] = useState('');
  const [isFocused, setIsFocused] = useState(false);
  const [NewsAPI, setNewsAPI] = useState(false);
  const [NewsAPIOptions, setNewsAPIOptions] = useState<categoryItem[]>([]);
  const [NyTimes, setNyTimes] = useState(false);
  const [NyTimesOptions, setNyTimesOptions] = useState<categoryItem[]>([]);
  const [Guardian, setGuardian] = useState(false);
  const [GuardianOptions, setGuardianOptions] = useState<categoryItem[]>([]);

  const handleStartDateChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setStartDate(e.target.value);
  };

  const handleEndDateChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setEndDate(e.target.value);
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    // Check if searchInputRef value is empty
    const searchInputValue = searchInputRef.current?.value;
    if (!searchInputValue || searchInputValue.trim() === '') {
      toast.error('Please enter a search query');
      return;
    }
    queryClient.invalidateQueries('search');

    const formData: FormData = {
      q: searchInputRef.current?.value || ''
    }

    if (startDate) formData.startDate = startDate;
    if (endDate) formData.endDate = endDate;

    if (NewsAPI) {
      formData.NewsAPI = "true";
      const NewsAPICategories = NewsAPIRef.current?.getSelectedItems();
      formData.NewsAPICategories = NewsAPICategories?.map((item: categoryItem) => item.key).join(",") || "";
    }

    if (NyTimes) {
      formData.NyTimes = "true";
      const NyTimesCategories = NyTimesRef.current?.getSelectedItems();
      formData.NyTimesCategories = NyTimesCategories?.map((item: categoryItem) => item.key).join(",") || "";
    }

    if (Guardian) {
      formData.Guardian = "true";
      const GuardianCategories = GuardianRef.current?.getSelectedItems();
      formData.GuardianCategories = GuardianCategories?.map((item: categoryItem) => item.key).join(",") || "";
    }

    const queryParams = new URLSearchParams(formData);
    const queryString = queryParams.toString();
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

  // css classes
  const formCSS = isFocused ? 'top-0 left-0 px-5 py-[4.5rem] fixed bg-white w-full h-full md:relative md:p-0' : 'relative';
  const searchInputCSS = isFocused ? '' : 'hidden md:block';
  const searchIconCSS = isFocused ? 'top-[5.2rem] left-7 absolute md:top-2.5 md:left-2' : 'top-2.5 -right-5 absolute md:left-2';
  const submitIconCSS = isFocused ? 'top-[4.6rem] right-6 block' : 'top-0.5 right-1 hidden md:block';
  const closeIconCSS = isFocused ? 'top-5 right-5 block md:hidden' : 'hidden'; 

  return (
    <form onSubmit={handleSubmit} className={`${formCSS}`} ref={formRef}>
      <button type="button" className={`${searchIconCSS}`} onClick={() => setIsFocused(true)}>
        <MagnifyingGlassIcon className="w-6 h-6 text-gray-400" />
      </button>

      <input
        type="text"
        placeholder="Search"
        className={`border-solid border-2 border-gray-300 p-2 w-full text-left pl-10 ${searchInputCSS}`}
        onFocus={() => setIsFocused(true)}
        ref={searchInputRef}
      />

      {isFocused && (
        <div className="relative border bg-white w-full p-3 flex flex-col gap-2 z-10 md:absolute">
          {/* NewsAPI Options */}
          <NewsSourceFilter 
            source={NewsAPI}
            setSource={setNewsAPI}
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
            setSource={setNyTimes}
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
            setSource={setGuardian}
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

      <button type="submit" className={`absolute ${submitIconCSS}`}>
        <ArrowRightCircleIcon className="w-10 h-10 text-gray-800"/>
      </button>
      <button type="button" className={`absolute ${closeIconCSS}`} onClick={() => setIsFocused(false)}>
        <XMarkIcon className="w-8 h-8 text-gray-800" />
      </button>
    </form>
  );
}

export default Search;
