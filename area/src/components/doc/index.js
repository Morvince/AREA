import React, { useState } from 'react'
import { BgColor, ContainerLeft, DiscordBox, TwitchBox, IntroText, SpotifyBox, BoxContentLeft, ContainerRight, BoxContentRight, GoogleBox, TwitterBox, GithubBox, ActionContainer, ReactionContainer } from './docElements'
import { Icon } from '@iconify/react';

const Doc = () => {
  const [discordOpen, setDiscordOpen] = useState(false);
  const [spotifyOpen, setSpotifyOpen] = useState(false);
  const [twitchOpen, setTwitchOpen] = useState(false);
  const [googleOpen, setGoogleOpen] = useState(false);
  const [twitterOpen, setTwitterOpen] = useState(false);
  const [githubOpen, setGithubOpen] = useState(false);

  const toggleDiscordOpen = () => {
    setDiscordOpen(!discordOpen);
    setSpotifyOpen(false);
    setTwitchOpen(false);
  };
  
  const toggleSpotifyOpen = () => {
    setSpotifyOpen(!spotifyOpen);
    setDiscordOpen(false);
    setTwitchOpen(false);
  };
  
  const toggleTwitchOpen = () => {
    setTwitchOpen(!twitchOpen);
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
        {discordOpen && <BoxContentLeft background="#5470d6"> <ActionContainer size="200%" line="50px"> Action : <br></br> - When the username is changed </ActionContainer> <ReactionContainer size="150%" line="35px"> Reactions : <br></br> - Write a channel message <br></br> - Send a private message with the bot <br></br> - React to a message with the bot <br></br> -Create a thread</ReactionContainer> </BoxContentLeft>}
        <SpotifyBox onClick={toggleSpotifyOpen}> 
          <Icon icon="logos:spotify-icon" width="100" height="100" />
        </SpotifyBox>
        {spotifyOpen && <BoxContentLeft background="#10a143"> <ActionContainer size="200%" line="50px"> Action : <br></br> - When a song is added to a playlist </ActionContainer> <ReactionContainer size="190%" line="40px"> Reactions : <br></br> - Change playlist details <br></br> - Add a random song from an artist to a playlist </ReactionContainer> </BoxContentLeft>}
        <TwitchBox onClick={toggleTwitchOpen}>
          <Icon icon="mdi:twitch" color="white" width="130" height="130" />
        </TwitchBox>
        {twitchOpen && <BoxContentLeft background="#6713e2"> <ActionContainer size="200%" line="50px"> Action : <br></br> - When a new user followed your channel </ActionContainer> <ReactionContainer size="200%" line="50px"> Reaction : <br></br> - Clean your stream chat</ReactionContainer> </BoxContentLeft>}
      </ContainerLeft>
      <ContainerRight>
        <GoogleBox onClick={toggleGoogleOpen}> 
          <Icon icon="logos:google-gmail" width="100" height="100" />
        </GoogleBox>
        {googleOpen && <BoxContentRight background="#d92516"> <ActionContainer size="150%" line="40px"> Action : <br></br> - When a new mail is received <br></br> - When a new mail from someone is received </ActionContainer> <ReactionContainer size="190%" line="40px"> Reactions : <br></br> - Send a mail to somebody </ReactionContainer> </BoxContentRight>}
        <TwitterBox onClick={toggleTwitterOpen}> 
          <Icon icon="skill-icons:twitter" width="100" height="100" />
        </TwitterBox>
        {twitterOpen && <BoxContentRight background="#1486cc"> <ActionContainer size="200%" line="50px"> Action : <br></br> - When a new tweet is posted </ActionContainer> <ReactionContainer size="200%" line="50px"> Reactions : <br></br> - Post a tweet with the message <br></br> - Like a tweet related to a given word</ReactionContainer> </BoxContentRight>}
        <GithubBox onClick={toggleGithubOpen}>
          <Icon icon="mdi:github" color="white" width="130" height="130" />
        </GithubBox>
        {githubOpen && <BoxContentRight background="#686f84"> <ActionContainer size="200%" line="50px"> Action : <br></br> - When a new commit is done to default branch </ActionContainer> <ReactionContainer size="190%" line="40px"> Reactions : <br></br> - Create an issue <br></br> - Create/Edit the README.md </ReactionContainer> </BoxContentRight>}
      </ContainerRight>
    </>
  )
};

export default Doc