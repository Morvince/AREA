import './App.css';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Home from './pages/Home';
import Sign from './pages/Sign';
import Landing from './pages/Landing';

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route exact path='/' element={<Landing/>} />
        <Route exact path='/home' element={<Home/>} />
        <Route exact path='/sign' element={<Sign/>} />
      </Routes>
    </BrowserRouter>
  );
}

export default App;
