import axios from "axios"
import { useMutation } from "react-query"

const spotifyConnected = async () => {
  return await axios.post("/spotify/connected", JSON.stringify({token: sessionStorage.getItem("token")}))
}

export const useSpotifyConnected = () => {
  return useMutation(spotifyConnected)
}

const spotifyConnect = async (data) => {
  return await axios.post("/spotify/connect", data)
}

export const useSpotifyConnect = () => {
  return useMutation(spotifyConnect, {
    onSuccess: (data) => {
      window.location.replace(data.data.authorization_url)
    }
  })
}

const spotifyAccess = async (data) => {
  return await axios.post("/spotify/get_access_token", data)
}

export const useSpotifyAccess = () => {
  return useMutation(spotifyAccess)
}





const discordConnected = async () => {
  return await axios.post("/discord/connected", JSON.stringify({token: sessionStorage.getItem("token")}))
}

export const useDiscordConnected = () => {
  return useMutation(discordConnected)
}

const discordConnect = async (data) => {
  return await axios.post("/discord/connect", data)
}

export const useDiscordConnect = () => {
  return useMutation(discordConnect, {
    onSuccess: (data) => {
      window.location.replace(data.data.authorization_url)
    }
  })
}

const discordAccess = async (data) => {
  return await axios.post("/discord/get_access_token", data)
}

export const useDiscordAccess = () => {
  return useMutation(discordAccess)
}