import { Navigate, Outlet } from 'react-router-dom';

const PrivateRoutes = () => {
  return (
    sessionStorage.getItem("token") ? <Outlet/> : <Navigate to="/sign"/>
  )
}

export default PrivateRoutes