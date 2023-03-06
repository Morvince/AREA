import React, { useEffect } from 'react'
import { useNavigate, useSearchParams } from "react-router-dom"
import { useSpotifyAccess, useDiscordAccess, useTwitchAccess, useGmailAccess, useTwitterAccess, useGithubAccess} from '../api/apiSettingsPage'

// page connect services
const ConnectServices = () => {
  const [params] = useSearchParams()
  const handleSpotifyAccess = useSpotifyAccess()
  const handleDiscordAccess = useDiscordAccess()
  const handleTwitchAccess = useTwitchAccess()
  const handleGmailAccess = useGmailAccess()
  const handleTwitterAccess = useTwitterAccess()
  const handleGithubAccess = useGithubAccess()
  const navigate = useNavigate()

  useEffect(() => {
    switch (sessionStorage.getItem("serviceToConnect")) {
      case "spotify":
        handleSpotifyAccess.mutate(JSON.stringify({
          state: params.get("state"),
          token: sessionStorage.getItem("token"),
          code: params.get("code"),
          redirect_uri: "http://localhost:8081/connectServices"
        }), {onSettled: () => {navigate("/home", {replace: true})}});
        break;
      case "discord":
        handleDiscordAccess.mutate(JSON.stringify({
        state: params.get("state"),
        token: sessionStorage.getItem("token"),
        code: params.get("code"),
        redirect_uri: "http://localhost:8081/connectServices"
        }), {onSettled: () => {navigate("/home", {replace: true})}});
        break;
      case "twitch":
        handleTwitchAccess.mutate(JSON.stringify({
        state: params.get("state"),
        token: sessionStorage.getItem("token"),
        code: params.get("code"),
        redirect_uri: "http://localhost:8081/connectServices"
        }), {onSettled: () => {navigate("/home", {replace: true})}});
        break;
      case "gmail":
        handleGmailAccess.mutate(JSON.stringify({
        state: params.get("state"),
        token: sessionStorage.getItem("token"),
        code: params.get("code"),
        redirect_uri: "http://localhost:8081/connectServices"
        }), {onSettled: () => {navigate("/home", {replace: true})}});
        break;
      case "twitter":
        handleTwitterAccess.mutate(JSON.stringify({
        state: params.get("state"),
        token: sessionStorage.getItem("token"),
        code: params.get("code"),
        redirect_uri: "http://localhost:8081/connectServices"
        }), {onSettled: () => {navigate("/home", {replace: true})}});
        break;
      case "github":
        handleGithubAccess.mutate(JSON.stringify({
        state: params.get("state"),
        token: sessionStorage.getItem("token"),
        code: params.get("code"),
        redirect_uri: "http://localhost:8081/connectServices"
        }), {onSettled: () => {navigate("/home", {replace: true})}});
        break;
    }
  }, [])

  return (
    <div style={{height:"100vh", display: "flex", justifyContent: "center", alignItems: "center"}}>
      <h1>Loading...</h1>
    </div>
  )
}

export default ConnectServices