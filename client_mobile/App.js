import { QueryClient, QueryClientProvider } from 'react-query'
import axios from "axios"
import Navigation from './Navigation';

axios.defaults.baseURL = "http://192.168.0.12:8080"
axios.defaults.validateStatus = function (status) { return status < 300 }
const queryClient = new QueryClient()

export default function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <Navigation/>
    </QueryClientProvider>
  );
}
