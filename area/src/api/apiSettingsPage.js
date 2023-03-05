import axios from "axios"
import { useMutation } from "react-query"

// SPOTIFY :

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



// TWITCH :


const twitchConnected = async () => {
  return await axios.post("/twitch/connected", JSON.stringify({token: sessionStorage.getItem("token")}))
}

export const useTwitchConnected = () => {
  return useMutation(twitchConnected)
}

const twitchConnect = async (data) => {
  return await axios.post("/twitch/connect", data)
}

export const useTwitchConnect = () => {
  return useMutation(twitchConnect, {
    onSuccess: (data) => {
      window.location.replace(data.data.authorization_url)
    }
  })
}

const twitchAccess = async (data) => {
  return await axios.post("/twitch/get_access_token", data)
}

export const useTwitchAccess = () => {
  return useMutation(twitchAccess)
}



// GMAIL :


const gmailConnected = async () => {
  return await axios.post("/gmail/connected", JSON.stringify({token: sessionStorage.getItem("token")}))
}

export const useGmailConnected = () => {
  return useMutation(gmailConnected)
}

const gmailConnect = async (data) => {
  return await axios.post("/gmail/connect", data)
}

export const useGmailConnect = () => {
  return useMutation(gmailConnect, {
    onSuccess: (data) => {
      window.location.replace(data.data.authorization_url)
    }
  })
}

const gmailAccess = async (data) => {
  return await axios.post("/gmail/get_access_token", data)
}

export const useGmailAccess = () => {
  return useMutation(gmailAccess)
}


// TWITTER :


const twitterConnected = async () => {
  return await axios.post("/twitter/connected", JSON.stringify({token: sessionStorage.getItem("token")}))
}

export const useTwitterConnected = () => {
  return useMutation(twitterConnected)
}

const twitterConnect = async (data) => {
  return await axios.post("/twitter/connect", data)
}

export const useTwitterConnect = () => {
  return useMutation(twitterConnect, {
    onSuccess: (data) => {
      window.location.replace(data.data.authorization_url)
    }
  })
}

const twitterAccess = async (data) => {
  return await axios.post("/twitter/get_access_token", data)
}

export const useTwitterAccess = () => {
  return useMutation(twitterAccess)
}


// GITHUB :


const githubConnected = async () => {
  return await axios.post("/github/connected", JSON.stringify({token: sessionStorage.getItem("token")}))
}

export const useGithubConnected = () => {
  return useMutation(githubConnected)
}

const githubConnect = async (data) => {
  return await axios.post("/github/connect", data)
}

export const useGithubConnect = () => {
  return useMutation(githubConnect, {
    onSuccess: (data) => {
      window.location.replace(data.data.authorization_url)
    }
  })
}

const githubAccess = async (data) => {
  return await axios.post("/github/get_access_token", data)
}

export const useGithubAccess = () => {
  return useMutation(githubAccess)
}