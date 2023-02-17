import { Navigate, Outlet } from 'react-router-dom';

const PrivateRoutes = () => {
  return (
    sessionStorage.getItem("token") ? <Outlet/> : <Navigate to="/login"/>
  )
}

export default PrivateRoutes