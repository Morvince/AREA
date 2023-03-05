import { QueryClient, QueryClientProvider } from 'react-query'
import axios from "axios"
import Navigation from './Navigation';
import AsyncStorage from '@react-native-async-storage/async-storage';

AsyncStorage.getItem("serverIp")
.then((res) => {
  if (res !== null)
    axios.defaults.baseURL = res
  else
    axios.defaults.baseURL = ""
})
axios.defaults.validateStatus = function (status) { return status < 300 }
const queryClient = new QueryClient()

export default function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <Navigation/>
    </QueryClientProvider>
  );
}
