import './App.css';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Home from './pages/Home';
import Sign from './pages/Sign';
import LandingPage from './pages/Landing';
import Settings from './pages/Settings';

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route exact path='/' element={<LandingPage/>} />
        <Route exact path='/home' element={<Home/>} />
        <Route exact path='/sign' element={<Sign/>} />
        <Route exact path='/settings' element={<Settings/>} />
      </Routes>
    </BrowserRouter>
  );
}

export default App;
