import React, { useState } from 'react';
import { QueryClient, QueryClientProvider } from 'react-query'
import MainContent from './components/MainContent';
// import Register from './components/Register';
// import Layout from './components/Layout';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import Header from './components/Header';
import { AuthProvider } from './context/AuthContext';
import { BrowserRouter } from 'react-router-dom'
import { DataProvider } from './context/DataContext';


const queryClient = new QueryClient();

const App = () => {
 
  return (
    <QueryClientProvider client={queryClient}>
      <AuthProvider>
        <DataProvider>
          <BrowserRouter>
            <div className="container mx-auto my-0 justify-self-center p-5 ">
              <Header />
              <MainContent />
              <ToastContainer />
            </div>
          </BrowserRouter>
        </DataProvider>
      </AuthProvider>
    </QueryClientProvider>
  );
};

export default App;
