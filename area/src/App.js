import './App.css';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import PrivateRoutes from './utils/PrivateRoutes';
import Home from './pages/Home';
import Sign from './pages/Sign';
import LandingPage from './pages/Landing';

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
        <Route element={<PrivateRoutes/>}>
          <Route path='/home' element={<Home/>}/>
        </Route>
        <Route path='*' element={<PageNotFound/>}/>
      </Routes>
    </BrowserRouter>
  );
}

export default App;
