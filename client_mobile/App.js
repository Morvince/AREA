import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { QueryClient, QueryClientProvider } from 'react-query'
import axios from "axios"
import Home from './src/pages/Home';
import SignIn from './src/pages/SignIn';
import SignUp from './src/pages/SignUp';

const Stack = createNativeStackNavigator();

axios.defaults.baseURL = "http://192.168.128.5:8080"
axios.defaults.validateStatus = function (status) { return status < 300 }
const queryClient = new QueryClient()

export default function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <NavigationContainer>
        <Stack.Navigator initialRouteName="SignIn" screenOptions={{headerShown: false}}>
          <Stack.Screen name="SignIn" component={SignIn}/>
          <Stack.Screen name="SignUp" component={SignUp}/>
          <Stack.Screen name="Home" component={Home}/>
        </Stack.Navigator>
      </NavigationContainer>
    </QueryClientProvider>
  );
}
