import React, { useState, useContext } from 'react'
import Login from './Login';
import Register from './Register';
import { AuthContext } from './../context/AuthContext';

type HeaderProps = {
    // isLoggedIn: boolean
}
const Header = ({ }: HeaderProps) => {
    const { isLoggedIn, handleLogout  } = useContext(AuthContext);
    const [showLoginForm, setShowLoginForm] = useState(false);
    const [showRegisterForm, setShowRegisterForm] = useState(false);

    const toggleRegisterForm = () => {
        setShowRegisterForm(!showRegisterForm);
        setShowLoginForm(false);
    }
    const toggleLoginForm = () => {
        setShowLoginForm(!showLoginForm);
        setShowRegisterForm(false);
    }
    const handleLogoutButton = () => {
        setShowRegisterForm(false);
        setShowLoginForm(false);
        handleLogout();
    }

  return (
    <header className="flex gap-10">
        <div className="">
            <img src="./public/news-aggregator-logo.png" alt="" width="250" height="32"/>
        </div>
        <div className="flex-grow">
            <button className="border-solid border-2 border-gray-300 p-2 w-full text-left">
                Search
            </button>
        </div>
        <div>
            {!isLoggedIn ? (
                <>
                    <button className="mx-5 bg-gray-800 text-white p-2" onClick={toggleRegisterForm}>Sign Up</button>
                    <button className="mx-5 p-2" onClick={toggleLoginForm}>Log In</button>
                    
                    {showLoginForm && <Login />}
                    {showRegisterForm && <Register />}
                </>
            ) : (
                <>
                    <button className="mx-5 bg-gray-800 text-white p-2" onClick={handleLogoutButton}>Log out</button>
                </>
            )}
        </div>
    </header>
  )
}

export default Header