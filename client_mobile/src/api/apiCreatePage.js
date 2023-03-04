import axios from "axios"
import { useMutation } from "react-query"
import AsyncStorage from '@react-native-async-storage/async-storage';

const getAllActions = async () => {
  return await axios.post("/action/get_all")
}

export const useGetAllActions = () => {
  return useMutation(getAllActions, {retry: 3})
}

const dropdownlistGetData = async (uri) => {
  const token = await AsyncStorage.getItem("token")
  return await axios.post(uri, JSON.stringify({token: token}))
}

export const useDropdownlistGetData = () => {
  return useMutation(dropdownlistGetData)
}


// SPOTIFY :


const spotifyConnected = async () => {
  const token = await AsyncStorage.getItem("token")
  return await axios.post("/spotify/connected", JSON.stringify({token: token}))
}

export const useSpotifyConnected = () => {
  return useMutation(spotifyConnected)
}


// DISCORD :


const discordConnected = async () => {
  const token = await AsyncStorage.getItem("token")
  return await axios.post("/discord/connected", JSON.stringify({token: token}))
}

export const useDiscordConnected = () => {
  return useMutation(discordConnected)
}


// TWITCH :


const twitchConnected = async () => {
  const token = await AsyncStorage.getItem("token")
  return await axios.post("/twitch/connected", JSON.stringify({token: token}))
}

export const useTwitchConnected = () => {
  return useMutation(twitchConnected)
}


// GMAIL :


const gmailConnected = async () => {
  const token = await AsyncStorage.getItem("token")
  return await axios.post("/gmail/connected", JSON.stringify({token: token}))
}

export const useGmailConnected = () => {
  return useMutation(gmailConnected)
}


// TWITTER :


const twitterConnected = async () => {
  const token = await AsyncStorage.getItem("token")
  return await axios.post("/twitter/connected", JSON.stringify({token: token}))
}

export const useTwitterConnected = () => {
  return useMutation(twitterConnected)
}


// GITHUB :


const githubConnected = async () => {
  const token = await AsyncStorage.getItem("token")
  return await axios.post("/github/connected", JSON.stringify({token: token}))
}

export const useGithubConnected = () => {
  return useMutation(githubConnected)
}