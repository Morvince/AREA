import React, { useState } from 'react'
import { BgColor, ContainerLeft, DiscordBox, TwitchBox, IntroText, SpotifyBox, BoxContentLeft, ContainerRight, BoxContentRight, GoogleBox, TwitterBox, GithubBox, ActionContainer, ReactionContainer } from './docElements'
import { Icon } from '@iconify/react';

const Doc = () => {
  
  // boolien to set the current state of the service's box to know if they are open or not
  const [discordOpen, setDiscordOpen] = useState(false);
  const [spotifyOpen, setSpotifyOpen] = useState(false);
  const [twitchOpen, setTwitchOpen] = useState(false);
  const [googleOpen, setGoogleOpen] = useState(false);
  const [twitterOpen, setTwitterOpen] = useState(false);
  const [githubOpen, setGithubOpen] = useState(false);

  // function that open the discord doc and close the others
  const toggleDiscordOpen = () => {
    setDiscordOpen(!discordOpen);
    setSpotifyOpen(false);
    setTwitchOpen(false);
  };
  
  // function that open the spotify doc and close the others
  const toggleSpotifyOpen = () => {
    setSpotifyOpen(!spotifyOpen);
    setDiscordOpen(false);
    setTwitchOpen(false);
  };
  
  // function that open the Twitch doc and close the others
  const toggleTwitchOpen = () => {
    setTwitchOpen(!twitchOpen);
    setDiscordOpen(false);
    setSpotifyOpen(false);
  };

  // function that open the Google doc and close the others
  const toggleGoogleOpen = () => {
    setGoogleOpen(!googleOpen);
    setTwitterOpen(false);
    setGithubOpen(false);
  };

  // function that open the Twitter doc and close the others
  const toggleTwitterOpen = () => {
    setTwitterOpen(!twitterOpen);
    setGoogleOpen(false);
    setGithubOpen(false);
  };

  // function that open the Github doc and close the others
  const toggleGithubOpen = () => {
    setGithubOpen(!githubOpen);
    setGoogleOpen(false);
    setTwitterOpen(false);
  };

  return (
    <>
      <BgColor  BgColor />
      {/* principal text on the page */}
      <IntroText lineheight="1.2" fontsize="30px" top="20%" left="10%" color="white" fontweight="bold" > Here you are going to find a list resuming all the actions and reactions about our different applications !  </IntroText>
      {/* Container Left that display the Discord Spotify and Twitch docs */}
      <ContainerLeft> 
        {/* text that fill the doc for Discord */}
        <DiscordBox onClick={toggleDiscordOpen}> 
          <Icon icon="skill-icons:discord" width="100" height="100" />
        </DiscordBox>
        {discordOpen && <BoxContentLeft background="#5470d6"> <ActionContainer size="200%" line="50px"> Action : <br></br> - When the username is changed </ActionContainer> <ReactionContainer size="150%" line="35px"> Reactions : <br></br> - Write a channel message <br></br> - Send a private message with the bot <br></br> - React to a message with the bot <br></br> -Create a thread</ReactionContainer> </BoxContentLeft>}
        {/* text that fill the doc for Spotify */}
        <SpotifyBox onClick={toggleSpotifyOpen}> 
          <Icon icon="logos:spotify-icon" width="100" height="100" />
        </SpotifyBox>
        {spotifyOpen && <BoxContentLeft background="#10a143"> <ActionContainer size="200%" line="50px"> Action : <br></br> - When a song is added to a playlist (name & Description) </ActionContainer> <ReactionContainer size="190%" line="40px"> Reactions : <br></br> - Change playlist details <br></br> - Add a random song from an artist to a playlist </ReactionContainer> </BoxContentLeft>}
        {/* text that fill the doc for Twitch */}
        <TwitchBox onClick={toggleTwitchOpen}>
          <Icon icon="mdi:twitch" color="white" width="130" height="130" />
        </TwitchBox>
        {twitchOpen && <BoxContentLeft background="#6713e2"> <ActionContainer size="200%" line="50px"> Action : <br></br> -  </ActionContainer> <ReactionContainer size="200%" line="50px"> Reactions : <br></br> -</ReactionContainer> </BoxContentLeft>}
      </ContainerLeft>
      {/* Container right that display The Google Twitter and Github docs */}
      <ContainerRight>
        {/* text that fill the doc for Google */}
        <GoogleBox onClick={toggleGoogleOpen}> 
          <Icon icon="logos:google-gmail" width="100" height="100" />
        </GoogleBox>
        {googleOpen && <BoxContentRight background="#d92516"> <ActionContainer size="150%" line="40px"> Action : <br></br> - When a new mail is received <br></br> - When a new mail from someone is received </ActionContainer> <ReactionContainer size="190%" line="40px"> Reactions : <br></br> - Send a mail to somebody </ReactionContainer> </BoxContentRight>}
        {/* text that fill the doc for Twitter */}
        <TwitterBox onClick={toggleTwitterOpen}> 
          <Icon icon="skill-icons:twitter" width="100" height="100" />
        </TwitterBox>
        {twitterOpen && <BoxContentRight background="#1486cc"> <ActionContainer size="200%" line="50px"> Action : <br></br> - </ActionContainer> <ReactionContainer size="200%" line="50px"> Reactions : <br></br> -</ReactionContainer> </BoxContentRight>}
        {/* text that fill the doc for Github */}
        <GithubBox onClick={toggleGithubOpen}>
          <Icon icon="mdi:github" color="white" width="130" height="130" />
        </GithubBox>
        {githubOpen && <BoxContentRight background="#686f84"> <ActionContainer size="200%" line="50px"> Action : <br></br> - When a new commit is done to default branch </ActionContainer> <ReactionContainer size="190%" line="40px"> Reactions : <br></br> - Create an issue <br></br> - Create/Edit the README.md </ReactionContainer> </BoxContentRight>}
      </ContainerRight>
    </>
  )
};

export default Doc