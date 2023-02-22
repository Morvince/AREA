import React, { useState } from 'react'
import { BgColor, ContainerLeft, DiscordBox, InstagramBox, IntroText, SpotifyBox, BoxContentLeft, ContainerRight, BoxContentRight, GoogleBox, TwitterBox, GithubBox } from './docElements'
import { Icon } from '@iconify/react';

const Doc = () => {
  const [discordOpen, setDiscordOpen] = useState(false);
  const [spotifyOpen, setSpotifyOpen] = useState(false);
  const [instagramOpen, setInstagramOpen] = useState(false);
  const [googleOpen, setGoogleOpen] = useState(false);
  const [twitterOpen, setTwitterOpen] = useState(false);
  const [githubOpen, setGithubOpen] = useState(false);

  const toggleDiscordOpen = () => {
    setDiscordOpen(!discordOpen);
    setSpotifyOpen(false);
    setInstagramOpen(false);
  };
  
  const toggleSpotifyOpen = () => {
    setSpotifyOpen(!spotifyOpen);
    setDiscordOpen(false);
    setInstagramOpen(false);
  };
  
  const toggleInstagramOpen = () => {
    setInstagramOpen(!instagramOpen);
    setDiscordOpen(false);
    setSpotifyOpen(false);
  };

  const toggleGoogleOpen = () => {
    setGoogleOpen(!googleOpen);
    setTwitterOpen(false);
    setGithubOpen(false);
  };

  const toggleTwitterOpen = () => {
    setTwitterOpen(!twitterOpen);
    setGoogleOpen(false);
    setGithubOpen(false);
  };

  const toggleGithubOpen = () => {
    setGithubOpen(!githubOpen);
    setGoogleOpen(false);
    setTwitterOpen(false);
  };

  return (
    <>
      <BgColor  BgColor />
      <IntroText lineheight="1.2" fontsize="30px" top="20%" left="10%" color="white" fontweight="bold" > Here you are going to find a list resuming all the actions and reactions about our different applications !  </IntroText>
      <ContainerLeft> 
        <DiscordBox onClick={toggleDiscordOpen}> 
          <Icon icon="skill-icons:discord" width="100" height="100" />
        </DiscordBox>
        {discordOpen && <BoxContentLeft background="#5470d6">Discord content here</BoxContentLeft>}
        <SpotifyBox onClick={toggleSpotifyOpen}> 
          <Icon icon="logos:spotify-icon" width="100" height="100" />
        </SpotifyBox>
        {spotifyOpen && <BoxContentLeft background="#10a143">Spotify content here</BoxContentLeft>}
        <InstagramBox onClick={toggleInstagramOpen}>
          <Icon icon="skill-icons:instagram" width="100" height="100" />
        </InstagramBox>
        {instagramOpen && <BoxContentLeft background="#c2134f">Instragram content here</BoxContentLeft>}
      </ContainerLeft>
      <ContainerRight>
        <GoogleBox onClick={toggleGoogleOpen}> 
          <Icon icon="logos:google-icon" width="100" height="100" />
        </GoogleBox>
        {googleOpen && <BoxContentRight background="#d92516">Google content here</BoxContentRight>}
        <TwitterBox onClick={toggleTwitterOpen}> 
          <Icon icon="skill-icons:twitter" width="100" height="100" />
        </TwitterBox>
        {twitterOpen && <BoxContentRight background="#1486cc">Twitter content here</BoxContentRight>}
        <GithubBox onClick={toggleGithubOpen}>
          <Icon icon="mdi:github" width="130" height="130" />
        </GithubBox>
        {githubOpen && <BoxContentRight background="#686f84">Github content here</BoxContentRight>}
      </ContainerRight>
    </>
  )
};

export default Doc