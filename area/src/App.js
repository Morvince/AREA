import './App.css';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import PrivateRoutes from './utils/PrivateRoutes';
import Home from './pages/Home';
import Sign from './pages/Sign';
import LandingPage from './pages/Landing';
import Settings from './pages/Settings';
import ConnectServices from './pages/ConnectServices';
import Login from './pages/Login';

const PageNotFound = () => {
  return (
    <h1>404: Page Not Found</h1>
  )
}

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route exact path='/' element={<LandingPage/>}/>
        <Route exact path='/sign' element={<Sign/>}/>
        <Route exact path='/login' element={<Login/>}/>
        <Route element={<PrivateRoutes/>}>
          <Route exact path='/home' element={<Home/>}/>
          <Route exact path='/settings' element={<Settings/>} />
          <Route exact path='/connectServices' element={<ConnectServices/>}/>
        </Route>
        <Route path='*' element={<PageNotFound/>}/>
      </Routes>
    </BrowserRouter>
  );
}

export default App;
