import React, { useState, useContext } from 'react';
import { UserCircleIcon } from '@heroicons/react/24/solid';
import { Link } from 'react-router-dom';
import { AuthContext } from './../context/AuthContext';
import Login from './Login';
import Register from './Register';
import SidePane from './SidePane';
import Search from './Search';

const Header: React.FC = () => {
  const { isLoggedIn, handleLogout } = useContext(AuthContext);
  const [showLoginForm, setShowLoginForm] = useState(false);
  const [showRegisterForm, setShowRegisterForm] = useState(false);
  const [showSidePane, setShowSidePane] = useState(false);

  const toggleRegisterForm = () => {
    setShowRegisterForm(!showRegisterForm);
    setShowLoginForm(false);
  };

  const toggleLoginForm = () => {
    setShowLoginForm(!showLoginForm);
    setShowRegisterForm(false);
  };

  const handleLogoutButton = () => {
    setShowRegisterForm(false);
    setShowLoginForm(false);
    handleLogout();
  };

  const toggleSidePane = () => {
    setShowSidePane(!showSidePane);
  };

  const renderAuthenticationButtons = () => {
    if (!isLoggedIn) {
      return (
        <>
          <button className="ml-5 bg-gray-800 text-white p-2" onClick={toggleRegisterForm}>
            Sign Up
          </button>
          <button className="ml-5 p-2" onClick={toggleLoginForm}>
            Log In
          </button>
          {showLoginForm && <Login />}
          {showRegisterForm && <Register />}
        </>
      );
    } else {
      return (
        <div className="flex">
          <button className="mx-1" onClick={toggleSidePane}>
            <UserCircleIcon className="w-10 h-10" />
          </button>
          <button className="ml-5 bg-gray-800 text-white p-2" onClick={handleLogoutButton}>
            Log out
          </button>
          {showSidePane && <SidePane toggleSidePane={toggleSidePane} />}
        </div>
      );
    }
  };

  return (
    <header className="flex gap-5 lg:gap-10">
        <div className="flex justify-center items-center z-20">
            <Link to="/">
                <img src="./news-aggregator-logo.png" alt="News Aggregator Logo" width="250" height="32" className="hidden md:block" />
                <img src="./favicon.png" alt="News Aggregator Logo" width="32" height="32" className="block md:hidden" />
            </Link>
        </div>
        <div className="flex-grow">
            <Search />
        </div>
        <div>{renderAuthenticationButtons()}</div>
    </header>
  );
};

export default Header;
