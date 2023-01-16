import React from 'react';
import './App.css';

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route exact path='/' element={<Home/>} />
      </Routes>
    </BrowserRouter>
  );
}

export default App;
