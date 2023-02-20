import React, { useEffect, useState } from 'react'
import { Rect, SettingsRect, Connect, Connected } from './settingsElements'
import { Icon } from '@iconify/react';
import { useSpotifyConnect, useSpotifyConnected, useDiscordConnect, useDiscordConnected, useInstagramConnect, useInstagramConnected } from '../../api/apiSettingsPage';

const Settings = () => {
  const handleSpotifyConnect = useSpotifyConnect();
  const isSpotifyConnected = useSpotifyConnected();
  const handleDiscordConnect = useDiscordConnect();
  const isDiscordConnected = useDiscordConnected();
  const handleInstagramConnect = useInstagramConnect();
  const isInstagramConnected = useInstagramConnected();

    useEffect(() => {
      isSpotifyConnected.mutate();
      isDiscordConnected.mutate();
      isInstagramConnected.mutate();
    }, []);

    const handleConnectServices = (event) => {
      event.preventDefault()
      switch (event.currentTarget.getAttribute("data-value")) {
        case "spotify":
          handleSpotifyConnect.mutate(JSON.stringify({redirect_uri: "http://localhost:8081/connectServices"}))
          break;
        case "discord":
          handleDiscordConnect.mutate(JSON.stringify({redirect_uri: "http://localhost:8081/connectServices"}))
          break;
        case "github":
          handleGithubConnect.mutate(JSON.stringify({redirect_uri: "http://localhost:8081/connectServices"}))
          break;
        default:
          break;
      }
    }

    return (
      <>
        <Rect top="0px" height="100%" color="#373b48" width="100%" Rect/>

        <SettingsRect top="200px" height="600px" color="#D4D3DC" width="25%" left="15%" SettingsRect/>
        <Icon icon="skill-icons:discord" width="100" style={{ position: 'absolute', left: '18%', top: "250px" }}/>
        <Icon icon="logos:spotify-icon" width="100" style={{ position: 'absolute', left: '18%', top: "450px" }}/>
        <Icon icon="skill-icons:instagram" width="100" style={{ position: 'absolute', left: '18%', top: "650px" }}/>
        {isDiscordConnected.isSuccess && isDiscordConnected.data.data.connected ?
          <Connected top="265px" left="25.5%" >Connected</Connected> :
          <Connect to="/" top="265px" left="26.5%" data-value="discord" onClick={handleConnectServices} >Connect</Connect>
        }
        {isSpotifyConnected.isSuccess && isSpotifyConnected.data.data.connected ?
          <Connected top="465px" left="25.5%" >Connected</Connected> :
          <Connect to="/" top="465px" left="26.5%" data-value="spotify" onClick={handleConnectServices} >Connect</Connect>
        }
        <Connect to="/" top="665px" left="26.5%" >Connect</Connect>

        <SettingsRect top="200px" height="600px" color="#D4D3DC" width="25%" left="60%" SettingsRect/>
        <Icon icon="logos:google-icon" width="100" style={{ position: 'absolute', left: '63%', top: "250px" }}/>
        <Icon icon="skill-icons:twitter" width="100" style={{ position: 'absolute', left: '63%', top: "450px" }}/>
        <Icon icon="logos:github-icon" width="100" style={{ position: 'absolute', left: '63%', top: "650px" }}/>
        <Connect to="/" top="265px" left="70.5%" >Connect</Connect>
        <Connect to="/" top="465px" left="71.5%" >Connect</Connect>
        <Connect to="/" top="665px" left="71.5%" >Connect</Connect>
        {isGithubConnected.isSuccess && isGithubConnected.data.data.connected ?
          <Connected top="665px" left="70.5%" >Connected</Connected> :
          <Connect to="/" top="665px" left="71.5%" data-value="github" onClick={handleConnectServices} >Connect</Connect>
        }
      </>
    )
};

export default Settings