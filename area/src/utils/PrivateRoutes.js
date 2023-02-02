import { Navigate, Outlet } from 'react-router-dom';

const PrivateRoutes = () => {
  console.log(sessionStorage.getItem("token"))
  return (
    sessionStorage.getItem("token") ? <Outlet/> : <Navigate to="/sign"/>
  )
}

export default PrivateRoutes