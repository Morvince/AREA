import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';
import { QueryClient, QueryClientProvider } from 'react-query'
import axios from "axios"
import { ReactQueryDevtools } from 'react-query/devtools' /* enlever lors de la mise en prod */

axios.defaults.baseURL = "http://localhost:8080"
axios.defaults.validateStatus = function (status) { return status < 300 }
const queryClient = new QueryClient()

const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(
  <React.StrictMode>
    <QueryClientProvider client={queryClient}>
      <App />
      <ReactQueryDevtools initialIsOpen={false}/> {/* enlever lors de la mise en prod */}
    </QueryClientProvider>
  </React.StrictMode>
);