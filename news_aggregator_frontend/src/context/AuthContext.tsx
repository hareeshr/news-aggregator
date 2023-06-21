import React, { createContext, useState } from 'react';

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

  const handleLogin = () => {
    setIsLoggedIn(true);
    console.log(localStorage.getItem('token'));
  };
  const handleLogout = () => {
    localStorage.removeItem('token');
    setIsLoggedIn(false);
  };

  return (
    <AuthContext.Provider value={{ isLoggedIn, handleLogin, handleLogout }}>
      {children}
    </AuthContext.Provider>
  );
};
