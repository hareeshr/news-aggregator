import HomeAllUser from './HomeAllUser'
import HomeLoggedInUser from './HomeLoggedInUser'
import { AuthContext } from '../context/AuthContext';
import { useContext } from 'react'

const MainContent = () => {
    const { isLoggedIn  } = useContext(AuthContext);

  return (
    <main className="py-10">
        { isLoggedIn
            ? <HomeLoggedInUser />
            : <HomeAllUser />
        }
    </main>
  )
}

export default MainContent