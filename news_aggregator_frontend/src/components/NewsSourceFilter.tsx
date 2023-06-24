import React from 'react';
import Multiselect from 'multiselect-react-dropdown';
import { categoryItem } from '../types/news';

type NewsSourceFilterProps = {
  source: boolean;
  setSource: React.Dispatch<React.SetStateAction<boolean>>;
  isLoadingData: boolean;
  categories: categoryItem[];
  id: string;
  name: string;
  selectedValues: categoryItem[];
  sourceRef: React.MutableRefObject<Multiselect | null>;
  singleSelect?: boolean;
};

const NewsSourceFilter: React.FC<NewsSourceFilterProps> = ({
  source,
  setSource,
  isLoadingData,
  categories,
  id,
  name,
  selectedValues,
  sourceRef,
  singleSelect = false,
}) => {
  const handleNews = (event: React.ChangeEvent<HTMLInputElement>) => {
    setSource(event.target.checked);
  };

  const multiSelectStyle = {
    option: {
      color: 'gray',
      backgroundColor: 'transparent',
    },
    chips: {
      border: '1px solid gray',
    },
    searchBox: {
      borderColor: '#7c7c7c',
      color: '#7c7c7c',
    },
    inputField: {
      color: 'gray',
    },
  };

  return (
    <>
      <div className="flex items-left mt-4">
        <input
          type="checkbox"
          id={id}
          checked={source}
          onChange={handleNews}
          className="mr-2"
        />
        <label htmlFor={id}>{name}</label>
      </div>
      {source && (
        <Multiselect
          options={categories}
          displayValue="name"
          showCheckbox={true}
          selectedValues={selectedValues}
          loading={isLoadingData}
          placeholder="Select categories..."
          style={multiSelectStyle}
          ref={sourceRef}
          singleSelect={singleSelect}
        />
      )}
    </>
  );
};

export default NewsSourceFilter;
