import { QueryClient, QueryClientProvider } from 'react-query';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { AuthProvider } from './context/AuthContext';
import { DataProvider } from './context/DataContext';
import Header from './components/Header';
import MainContent from './components/MainContent';
import SearchContainer from './components/SearchContainer';

const queryClient = new QueryClient();

const App = () => {
  return (
    <QueryClientProvider client={queryClient}>
      <AuthProvider>
        <DataProvider>
          <BrowserRouter>
            <div className="container mx-auto my-0 justify-self-center p-5">
              <Header />
              <main className="py-10">
                <Routes>
                  <Route path="/" element={<MainContent />} />
                  <Route path="/search" element={<SearchContainer />} />
                </Routes>
              </main>
              <ToastContainer />
            </div>
          </BrowserRouter>
        </DataProvider>
      </AuthProvider>
    </QueryClientProvider>
  );
};

export default App;
