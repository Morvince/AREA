import React, { useEffect } from 'react'
import { Navigate, useSearchParams } from "react-router-dom"
import { useSpotifyAccess, useDiscordAccess, useGithubAccess } from '../api/apiSettingsPage'

const ConnectServices = () => {
  const [params] = useSearchParams()
  const handleSpotifyAccess = useSpotifyAccess()
  const handleDiscordAccess = useDiscordAccess()
  const handleGithubAccess = useGithubAccess()
  useEffect(() => {
    handleSpotifyAccess.mutate(JSON.stringify({
      state: params.get("state"),
      token: sessionStorage.getItem("token"),
      code: params.get("code"),
      redirect_uri: "http://localhost:8081/connectServices"
    }))
    handleDiscordAccess.mutate(JSON.stringify({
      state: params.get("state"),
      token: sessionStorage.getItem("token"),
      code: params.get("code"),
      redirect_uri: "http://localhost:8081/connectServices"
    }))
    handleGithubAccess.mutate(JSON.stringify({
      state: params.get("state"),
      token: sessionStorage.getItem("token"),
      code: params.get("code"),
      redirect_uri: "http://localhost:8081/connectServices"
    }))
  }, [])

  if (handleSpotifyAccess.isIdle || handleSpotifyAccess.isLoading)
    return (
      <div style={{height:"100vh", display: "flex", justifyContent: "center", alignItems: "center"}}>
        <h1>Loading...</h1>
      </div>
    )
  else if (handleDiscordAccess.isIdle || handleDiscordAccess.isLoading)
    return (
      <div style={{height:"100vh", display: "flex", justifyContent: "center", alignItems: "center"}}>
        <h1>Loading...</h1>
      </div>
    )
  else if (handleGithubAccess.isIdle || handleGithubAccess.isLoading)
    return (
      <div style={{height:"100vh", display: "flex", justifyContent: "center", alignItems: "center"}}>
        <h1>Loading...</h1>
      </div>
    )
  else
      return (
        <Navigate to="/settings" replace={true}/>
      )
}

export default ConnectServices