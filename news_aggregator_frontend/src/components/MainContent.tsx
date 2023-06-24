import { useContext } from 'react'
import { AuthContext } from '../context/AuthContext';
import HomeAllUser from './HomeAllUser'
import HomeLoggedInUser from './HomeLoggedInUser'

const MainContent = () => {
    const { isLoggedIn  } = useContext(AuthContext);

  return (
    <>
      { isLoggedIn
          ? <HomeLoggedInUser />
          : <HomeAllUser />
      }
    </>
  )
}

export default MainContent