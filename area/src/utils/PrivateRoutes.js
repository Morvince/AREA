import { Navigate, Outlet } from 'react-router-dom';

// function to create the private routes
const PrivateRoutes = () => {
  return (
    sessionStorage.getItem("token") ? <Outlet/> : <Navigate to="/login"/>
  )
}

export default PrivateRoutes