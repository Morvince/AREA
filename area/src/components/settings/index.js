import React, { useEffect, useState } from 'react'
import { Rect, SettingsRect, Connect, Connected } from './settingsElements'
import { Icon } from '@iconify/react';
import { useSpotifyConnect, useSpotifyConnected } from '../../api/apiSettingsPage';

const Settings = () => {
    const handleSpotifyConnect = useSpotifyConnect()
    const isSpotifyConnected = useSpotifyConnected()

    useEffect(() => {
      isSpotifyConnected.mutate()
    }, []);

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
        <Connect top="265px" left="400px" >Connect</Connect>
        {isSpotifyConnected.isSuccess && isSpotifyConnected.data.data.connected ?
          <Connected top="465px" left="400px" >Connected</Connected> :
          <Connect to="/" top="465px" left="400px" data-value="spotify" onClick={handleConnectServices} >Connect</Connect>
        }

        <Connect to="/" top="665px" left="400px" >Connect</Connect>

        <SettingsRect top="200px" height="600px" color="#D4D3DC" width="700px" left="950px" SettingsRect/>
        <Icon icon="logos:google-icon" width="100" style={{ position: 'absolute', left: '1000px', top: "250px" }}/>
        <Icon icon="skill-icons:twitter" width="100" style={{ position: 'absolute', left: '1000px', top: "450px" }}/>
        <Icon icon="logos:openai-icon" width="100" style={{ position: 'absolute', left: '1000px', top: "650px" }}/>
        <Connect to="/" top="265px" left="1300px" >Connect</Connect>
        <Connect to="/" top="465px" left="1300px" >Connect</Connect>
        <Connect to="/" top="665px" left="1300px" >Connect</Connect>
      </>
    )
};

export default Settings