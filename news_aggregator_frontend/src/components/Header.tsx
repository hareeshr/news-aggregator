import { useState, useContext } from 'react'
import Login from './Login';
import Register from './Register';
import { AuthContext } from './../context/AuthContext';
import { UserCircleIcon } from '@heroicons/react/24/solid';
import SidePane from './SidePane';

type HeaderProps = {
    // isLoggedIn: boolean
}
const Header = ({ }: HeaderProps) => {
    const { isLoggedIn, handleLogout  } = useContext(AuthContext);
    const [showLoginForm, setShowLoginForm] = useState(false);
    const [showRegisterForm, setShowRegisterForm] = useState(false);
    const [showSidePane, setShowSidePane] = useState(false);

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
    const toggleSidePane = () => {
        setShowSidePane(!showSidePane);
    }

  return (
    <header className="flex gap-10">
        <div className="flex justify-center items-center">
            <img src="./news-aggregator-logo.png" alt="News Aggregator Logo" width="250" height="32" 
                className="hidden md:block"/>
            <img src="./favicon.png" alt="News Aggregator Logo" width="32" height="32" 
                className="block md:hidden"/>
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
                <div className="flex">
                    <button className="mx-1" onClick={toggleSidePane}>
                        <UserCircleIcon className="w-10 h-10"/>
                    </button>
                    <button className="mx-5 bg-gray-800 text-white p-2" onClick={handleLogoutButton}>Log out</button>

                    {showSidePane && <SidePane toggleSidePane={toggleSidePane} />}
                </div>
            )}
        </div>
    </header>
  )
}

export default Header