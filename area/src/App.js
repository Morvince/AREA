import './App.css';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import PrivateRoutes from './utils/PrivateRoutes';
import Home from './pages/Home';
import Sign from './pages/Sign';
import LandingPage from './pages/Landing';
import ConnectServices from './pages/ConnectServices';
import Login from './pages/Login';
import Areas from './pages/Areas'
import Documentation from './pages/Documentation'
import WaitingForRegistration from './pages/WaitingForRegistration'
import Validate from './pages/Validate'

// function that display Page Not Found if the page isn't found
const PageNotFound = () => {
  return (
    <h1>404: Page Not Found</h1>
  )
}

// function with all the route of the website
function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route exact path='/' element={<LandingPage/>}/>
        <Route exact path='/sign' element={<Sign/>}/>
        <Route exact path='/login' element={<Login/>}/>
        <Route exact path='/waiting_for_registration' element={<WaitingForRegistration/>}/>
        <Route element={<PrivateRoutes/>}>
        <Route exact path='/home' element={<Home/>}/>
        <Route exact path='/connectServices' element={<ConnectServices/>}/>
        <Route exact path='/areas' element={<Areas/>}/>
        <Route exact path='/doc' element={<Documentation/>}/>
        <Route exact path='/validate' element={<Validate/>}/>
        </Route>
        <Route path='*' element={<PageNotFound/>}/>
      </Routes>
    </BrowserRouter>
  );
}

export default App;
