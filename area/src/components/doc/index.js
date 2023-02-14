import React, { useState } from 'react'
import { BgColor, ContainerLeft, DiscordBox, InstagramBox, IntroText, SpotifyBox, BoxContent } from './docElements'
import { Icon } from '@iconify/react';

const Doc = () => {
  const [discordOpen, setDiscordOpen] = useState(false);
  const [spotifyOpen, setSpotifyOpen] = useState(false);
  const [instagramOpen, setInstagramOpen] = useState(false);

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

  return (
    <>
      <BgColor  BgColor />
      <IntroText lineheight="1.2" fontsize="30px" top="20%" left="10%" color="white" fontweight="bold" > Here you are going to find a list resuming all the actions and reactions about our different applications !  </IntroText>
      <ContainerLeft> 
        <DiscordBox onClick={toggleDiscordOpen}> 
          <Icon icon="skill-icons:discord" width="100" height="100" />
        </DiscordBox>
        {discordOpen && <BoxContent background="#5470d6">Discord content here</BoxContent>}
        <SpotifyBox onClick={toggleSpotifyOpen}> 
          <Icon icon="logos:spotify-icon" width="100" height="100" />
        </SpotifyBox>
        {spotifyOpen && <BoxContent background="#10a143">Spotify content here</BoxContent>}
        <InstagramBox onClick={toggleInstagramOpen}>
          <Icon icon="skill-icons:instagram" width="100" height="100" />
        </InstagramBox>
        {instagramOpen && <BoxContent background="#c2134f">Instragram content here</BoxContent>}
      </ContainerLeft>
    </>
  )
};

export default Doc