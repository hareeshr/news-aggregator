import React from 'react';
import { useQuery } from 'react-query';
import { UserCircleIcon } from '@heroicons/react/24/solid';
import { API_BASE_URL } from './../config/api';

type UserData = {
  name: string;
  email: string;
}

const UserData = () => {
  const token = localStorage.getItem('token');

  const { data, isLoading } = useQuery<UserData>('userData', async () => {
    const response = await fetch(`${API_BASE_URL}/user/details`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    if (!response.ok) {
      throw new Error('Failed to fetch user data');
    }

    return response.json();
  });

  return (
    <>
      <UserCircleIcon className="w-20 h-20 text-gray-800" />
      {!isLoading && (
        <div className="text-center">
          <p className="font-medium">{data?.name}</p>
          <p className="text-sm">{data?.email}</p>
        </div>
      )}
    </>
  );
};

export default UserData;
