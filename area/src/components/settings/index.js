import React from 'react'
import { Rect, SettingsRect, Button } from './settingsElements'
import { Icon } from '@iconify/react';
import { useSpotifyConnect } from '../../api/apiSettingsPage';

const Settings = () => {
    const handleSpotifyConnect = useSpotifyConnect()

    const handleConnectServices = (event) => {
      event.preventDefault()
      switch (event.currentTarget.getAttribute("data-value")) {
        case "spotify":
          handleSpotifyConnect.mutate(JSON.stringify({redirect_uri: "http://localhost:8081/connectServices"}))
          break;
        default:
          break;
      }
    }
    return (
      <>
        <Rect top="0px" height="100%" color="#373b48" width="100%" Rect/>

        <SettingsRect top="200px" height="600px" color="#D4D3DC" width="700px" left="50px" SettingsRect/>
        <Icon icon="skill-icons:discord" width="100" style={{ position: 'absolute', left: '100px', top: "250px" }}/>
        <Icon icon="logos:spotify-icon" width="100" style={{ position: 'absolute', left: '100px', top: "450px" }}/>
        <Icon icon="skill-icons:instagram" width="100" style={{ position: 'absolute', left: '100px', top: "650px" }}/>
        <Button to="/" top="265px" left="400px" >Connect</Button>
        <Button to="/" top="465px" left="400px" data-value="spotify" onClick={handleConnectServices} >Connect</Button>
        <Button to="/" top="665px" left="400px" >Connect</Button>

        <SettingsRect top="200px" height="600px" color="#D4D3DC" width="700px" left="950px" SettingsRect/>
        <Icon icon="logos:google-icon" width="100" style={{ position: 'absolute', left: '1000px', top: "250px" }}/>
        <Icon icon="skill-icons:twitter" width="100" style={{ position: 'absolute', left: '1000px', top: "450px" }}/>
        <Icon icon="logos:openai-icon" width="100" style={{ position: 'absolute', left: '1000px', top: "650px" }}/>
        <Button to="/" top="265px" left="1300px" >Connect</Button>
        <Button to="/" top="465px" left="1300px" >Connect</Button>
        <Button to="/" top="665px" left="1300px" >Connect</Button>
      </>
    )
};

export default Settings