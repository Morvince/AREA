import React, { useEffect } from 'react'
import { useNavigate, useSearchParams } from "react-router-dom"
import { useSpotifyAccess, useDiscordAccess, useInstagramAccess, useGoogleAccess, useTwitterAccess, useGithubAccess} from '../api/apiSettingsPage'
import { useAddAutomation } from '../api/apiServicesPage';

const ConnectServices = () => {
  const [params] = useSearchParams()
  const handleSpotifyAccess = useSpotifyAccess()
  const handleDiscordAccess = useDiscordAccess()
  const handleInstagramAccess = useInstagramAccess()
  const handleGoogleAccess = useGoogleAccess()
  const handleTwitterAccess = useTwitterAccess()
  const handleGithubAccess = useGithubAccess()

  const tmpAutomation = useAddAutomation();
  const navigate = useNavigate()

  useEffect(() => {
    switch (sessionStorage.getItem("serviceToConnect")) {
      case "spotify":
        handleSpotifyAccess.mutate(JSON.stringify({
          state: params.get("state"),
          token: sessionStorage.getItem("token"),
          code: params.get("code"),
          redirect_uri: "http://localhost:8081/connectServices"
        }), {onSettled: () => { tmpAutomation.mutate(null, {onSuccess: (data) => { navigate("/home", {replace: true, state: {automationId: data.data}}) }}) }});
        break;
      case "discord":
        handleDiscordAccess.mutate(JSON.stringify({
        state: params.get("state"),
        token: sessionStorage.getItem("token"),
        code: params.get("code"),
        redirect_uri: "http://localhost:8081/connectServices"
        }), {onSettled: () => { tmpAutomation.mutate(null, {onSuccess: (data) => { navigate("/home", {replace: true, state: {automationId: data.data}}) }}) }});
        break;
      case "instagram":
        handleInstagramAccess.mutate(JSON.stringify({
        state: params.get("state"),
        token: sessionStorage.getItem("token"),
        code: params.get("code"),
        redirect_uri: "http://localhost:8081/connectServices"
        }), {onSettled: () => { tmpAutomation.mutate(null, {onSuccess: (data) => { navigate("/home", {replace: true, state: {automationId: data.data}}) }}) }});
        break;
      case "google":
        handleGoogleAccess.mutate(JSON.stringify({
        state: params.get("state"),
        token: sessionStorage.getItem("token"),
        code: params.get("code"),
        redirect_uri: "http://localhost:8081/connectServices"
        }), {onSettled: () => { tmpAutomation.mutate(null, {onSuccess: (data) => { navigate("/home", {replace: true, state: {automationId: data.data}}) }}) }});
        break;
      case "twitter":
        handleTwitterAccess.mutate(JSON.stringify({
        state: params.get("state"),
        token: sessionStorage.getItem("token"),
        code: params.get("code"),
        redirect_uri: "http://localhost:8081/connectServices"
        }), {onSettled: () => { tmpAutomation.mutate(null, {onSuccess: (data) => { navigate("/home", {replace: true, state: {automationId: data.data}}) }}) }});
        break;
      case "github":
        handleGithubAccess.mutate(JSON.stringify({
        state: params.get("state"),
        token: sessionStorage.getItem("token"),
        code: params.get("code"),
        redirect_uri: "http://localhost:8081/connectServices"
        }), {onSettled: () => { tmpAutomation.mutate(null, {onSuccess: (data) => { navigate("/home", {replace: true, state: {automationId: data.data}}) }}) }});
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