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

  const navigate = useNavigate();

  const tmpAutomation = useAddAutomation();

  function redirect() {
      tmpAutomation.mutate();
  }

  useEffect(() => {
    console.log(params.get("service"))
    switch (params.get("service")) {
      case "Spotify":
        handleSpotifyAccess.mutate(JSON.stringify({
          state: params.get("state"),
          token: sessionStorage.getItem("token"),
          code: params.get("code"),
          redirect_uri: "http://localhost:8081/connectServices"
        }));
        break;
      case "Discord":
        console.log("passe dans le case\n")
        handleDiscordAccess.mutate(JSON.stringify({
        state: params.get("state"),
        token: sessionStorage.getItem("token"),
        code: params.get("code"),
        redirect_uri: "http://localhost:8081/connectServices"
        }));
        break;
      case "Instagram":
        handleInstagramAccess.mutate(JSON.stringify({
        state: params.get("state"),
        token: sessionStorage.getItem("token"),
        code: params.get("code"),
        redirect_uri: "http://localhost:8081/connectServices"
        }));
        break;
      case "Google":
        handleGoogleAccess.mutate(JSON.stringify({
        state: params.get("state"),
        token: sessionStorage.getItem("token"),
        code: params.get("code"),
        redirect_uri: "http://localhost:8081/connectServices"
        }));
        break;
      case "Twitter":
        handleTwitterAccess.mutate(JSON.stringify({
        state: params.get("state"),
        token: sessionStorage.getItem("token"),
        code: params.get("code"),
        redirect_uri: "http://localhost:8081/connectServices"
        }));
        break;
      case "Github":
        handleGithubAccess.mutate(JSON.stringify({
        state: params.get("state"),
        token: sessionStorage.getItem("token"),
        code: params.get("code"),
        redirect_uri: "http://localhost:8081/connectServices"
        }));
        break;
    }
  }, [])

  console.log(handleDiscordAccess.status);
  // || handleSpotifyAccess.isIdle || handleSpotifyAccess.isLoading || handleInstagramAccess.isIdle || handleInstagramAccess.isLoading || handleGoogleAccess.isIdle || handleGoogleAccess.isLoading || handleTwitterAccess.isIdle || handleSpotifyAccess.isLoading || handleGithubAccess.isIdle || handleGithubAccess.isLoading
  if (handleDiscordAccess.isIdle || handleDiscordAccess.isLoading )
    return (
      <div style={{height:"100vh", display: "flex", justifyContent: "center", alignItems: "center"}}>
        <h1>Loading...</h1>
      </div>
    )
  else
    redirect();
    if (tmpAutomation.isSuccess)
      navigate("/home", { replace: true, state: { automationId: tmpAutomation.data.data } }) 
}

export default ConnectServices