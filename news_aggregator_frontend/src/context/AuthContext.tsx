import React, { createContext, useState } from 'react';
import { useQueryClient } from 'react-query';

type AuthContextType = {
  isLoggedIn: boolean;
  handleLogin: () => void;
  handleLogout: () => void;
};

export const AuthContext = createContext<AuthContextType>({
  isLoggedIn: false,
  handleLogin: () => {},
  handleLogout: () => {},
});

export const AuthProvider = ({ children }: { children:React.ReactNode}) => {
  const [isLoggedIn, setIsLoggedIn] = useState(!!localStorage.getItem('token'));
  const queryClient = useQueryClient();

  const handleLogin = () => {
    setIsLoggedIn(true);
  };
  const handleLogout = () => {
    localStorage.removeItem('token');
    setIsLoggedIn(false);
    queryClient.clear();
  };

  return (
    <AuthContext.Provider value={{ isLoggedIn, handleLogin, handleLogout }}>
      {children}
    </AuthContext.Provider>
  );
};
