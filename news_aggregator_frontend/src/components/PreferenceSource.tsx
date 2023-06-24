import React from 'react'
import Multiselect from 'multiselect-react-dropdown';
import { categoryItem } from './../types/news'


type PreferenceSourceProps = {
    source: boolean,
    setSouce: React.Dispatch<React.SetStateAction<boolean>>,
    isLoadingData: boolean,
    categories: categoryItem[],
    id: string,
    name: string,
    selectedValues: categoryItem[],
    sourceRef: React.MutableRefObject<Multiselect | null>,
    singleSelect?: boolean,
}
const PreferenceSource = ({ source, isLoadingData, setSouce, sourceRef, categories, selectedValues,  id, name, singleSelect= false }: PreferenceSourceProps) => {

  const handlePreference = (event: React.ChangeEvent<HTMLInputElement>) => {
    setSouce(event.target.checked);
  };

  
  const MultiSelectStyle = {
    option: {
        color: 'gray',
        backgroundColor: 'transparent',
      },
      chips: {
        border: '1px solid gray',
      },
      searchBox: {
        borderColor: '#7c7c7c',
        color: '#7c7c7c'
      },
      inputField: {
        color: 'gray',
      },
  }

  return (
    <>
        <div className="flex items-left mt-4">
            <input
                type="checkbox"
                id={id}
                checked={source}
                onChange={handlePreference}
                className="mr-2"
            />
            <label htmlFor={id}>{name}</label>
        </div>
        {source && <Multiselect 
            options={categories} 
            displayValue="name" 
            showCheckbox={true}
            selectedValues={selectedValues}
            loading={isLoadingData}
            placeholder="Select categories..."
            style={MultiSelectStyle}
            ref={sourceRef}
            singleSelect={singleSelect}
            />
        }
    </>
  )
}

export default PreferenceSource