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




// DISCORD :


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



// INSTAGRAM :


const instagramConnected = async () => {
  return await axios.post("/instagram/connected", JSON.stringify({token: sessionStorage.getItem("token")}))
}

export const useInstagramConnected = () => {
  return useMutation(instagramConnected)
}

const instagramConnect = async (data) => {
  return await axios.post("/instagram/connect", data)
}

export const useInstagramConnect = () => {
  return useMutation(instagramConnect, {
    onSuccess: (data) => {
      window.location.replace(data.data.authorization_url)
    }
  })
}

const instagramAccess = async (data) => {
  return await axios.post("/instagram/get_access_token", data)
}

export const useInstagramAccess = () => {
  return useMutation(instagramAccess)
}



// GOOGLE :


const googleConnected = async () => {
  return await axios.post("/google/connected", JSON.stringify({token: sessionStorage.getItem("token")}))
}

export const useGoogleConnected = () => {
  return useMutation(googleConnected)
}

const googleConnect = async (data) => {
  return await axios.post("/google/connect", data)
}

export const useGoogleConnect = () => {
  return useMutation(googleConnect, {
    onSuccess: (data) => {
      window.location.replace(data.data.authorization_url)
    }
  })
}

const googleAccess = async (data) => {
  return await axios.post("/google/get_access_token", data)
}

export const useGoogleAccess = () => {
  return useMutation(googleAccess)
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