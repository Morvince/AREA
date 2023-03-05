import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs'
import { Ionicons } from '@expo/vector-icons';
import EnterIpAddress from './src/pages/EnterIpAddress';
import SignIn from './src/pages/SignIn';
import SignUp from './src/pages/SignUp';
import Create from './src/pages/Create';
import MyAreas from './src/pages/MyAreas';
import { darkGray, lightPurple } from './src/color';

const Stack = createNativeStackNavigator()
const Tab = createBottomTabNavigator()

function Home() {
  return (
    <Tab.Navigator initialRouteName="Home"
      screenOptions={({route}) => ({
        tabBarIcon: ({focused, color, size}) => {
          let iconName;

          if (route.name === "Create") {
            iconName = focused ? "create" : "create-outline"
          } else if (route.name === "MyAreas") {
            iconName = focused ? "md-home" : "md-home-outline"
          }

          return (
            <Ionicons name={iconName} size={32} color={color} />
          )
        },
        tabBarActiveTintColor: lightPurple,
        tabBarInactiveTintColor: darkGray,
        tabBarLabelStyle: { paddingBottom: 5, fontSize: 15 },
        tabBarStyle: { paddingTop: 5, height: 70 },
        headerShown: false
      })}
    >
      <Tab.Screen name="MyAreas" component={MyAreas}/>
      <Tab.Screen name="Create" component={Create}/>
    </Tab.Navigator>
  )
}

export default function Navigation() {
  return (
    <NavigationContainer>
      <Stack.Navigator initialRouteName="EnterIpAddress" screenOptions={{headerShown: false}}>
        <Stack.Screen name="EnterIpAddress" component={EnterIpAddress}/>
        <Stack.Screen name="SignIn" component={SignIn}/>
        <Stack.Screen name="SignUp" component={SignUp}/>
        <Stack.Screen name="Home" component={Home}/>
      </Stack.Navigator>
    </NavigationContainer>
  )
}