import { UserCircleIcon } from '@heroicons/react/24/solid'
import { useQuery } from 'react-query';

interface UserData {
    name: string;
    email: string;
}

const UserData = () => {
    const token = localStorage.getItem('token');
    
  const { data, isLoading } = useQuery<UserData>('userData', async () => {
    const response = await fetch('http://localhost:8000/api/user/details', {
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
        <UserCircleIcon className="w-20 h-20 text-gray-800"/>
        {!isLoading && (
            <div className="text-center">
                <p className="font-medium">{data?.name}</p>
                <p className="text-sm">{data?.email}</p>
            </div>
        )}
    </>
  )
}

export default UserData